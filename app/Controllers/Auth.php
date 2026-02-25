<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Services;

class Auth extends BaseController
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
    }

    public function register()
    {
        return view('auth/register', ['title' => lang('App.create_account')]);
    }

    public function store()
    {
        // ensure request is POST
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/register');
        }

        // fetch individual fields to avoid null array issues
        $name     = $this->request->getPost('name');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // basic validation
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // save user - model has its own validation rules which expect a
        // `password` field, but we are storing a hash.  We already ran manual
        // validation above so skip model validation here to avoid the mismatch.
        $saveData = [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];

        if (! $this->userModel->skipValidation(true)->save($saveData)) {
            // grab any errors from the model (should be none since we skipped, but
            // just in case something else failed)
            $errors = $this->userModel->errors();
            if (empty($errors)) {
                $errors = ['Unable to save user.'];
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // automatically log the user in
        $user = $this->userModel->where('email', $email)->first();
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_name', $user['name']);

        return redirect()->to('/dashboard');
    }

    public function login()
    {
        return view('auth/login', ['title' => lang('App.sign_in')]);
    }

    public function attempt()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();
        if (! $user || empty($user['password_hash']) || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', lang('App.invalid_credentials'));
        }

        $this->session->set('user_id', $user['id']);
        $this->session->set('user_name', $user['name']);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        $locale = $this->session->get('site_locale');
        $this->session->destroy();
        if (! empty($locale)) {
            session()->set('site_locale', $locale);
        }
        return redirect()->to('/login');
    }

    public function socialRedirect(string $provider)
    {
        $provider = strtolower($provider);
        $config = $this->getSocialConfig($provider);
        if ($config === null) {
            return redirect()->to('/login')->with('error', 'OAuth provider not configured');
        }

        $state = bin2hex(random_bytes(16));
        $this->session->set('oauth_state_' . $provider, $state);

        $redirectUri = site_url('auth/' . $provider . '/callback');

        if ($provider === 'google') {
            $query = http_build_query([
                'client_id' => $config['client_id'],
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'openid email profile',
                'state' => $state,
                'access_type' => 'online',
                'prompt' => 'select_account',
            ]);
            return redirect()->to('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
        }

        if ($provider === 'github') {
            $query = http_build_query([
                'client_id' => $config['client_id'],
                'redirect_uri' => $redirectUri,
                'scope' => 'read:user user:email',
                'state' => $state,
            ]);
            return redirect()->to('https://github.com/login/oauth/authorize?' . $query);
        }

        return redirect()->to('/login')->with('error', 'Unsupported OAuth provider');
    }

    public function socialCallback(string $provider)
    {
        $provider = strtolower($provider);
        $config = $this->getSocialConfig($provider);
        if ($config === null) {
            return redirect()->to('/login')->with('error', 'OAuth provider not configured');
        }

        $expectedState = $this->session->get('oauth_state_' . $provider);
        $this->session->remove('oauth_state_' . $provider);
        $incomingState = (string) $this->request->getGet('state');
        if (empty($expectedState) || ! hash_equals($expectedState, $incomingState)) {
            return redirect()->to('/login')->with('error', 'OAuth state is invalid');
        }

        $error = $this->request->getGet('error');
        if (! empty($error)) {
            return redirect()->to('/login')->with('error', 'OAuth failed: ' . esc((string) $error));
        }

        $code = (string) $this->request->getGet('code');
        if (empty($code)) {
            return redirect()->to('/login')->with('error', 'OAuth authorization code not received');
        }

        $redirectUri = site_url('auth/' . $provider . '/callback');
        $token = $this->exchangeCodeForToken($provider, $code, $redirectUri, $config);
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Unable to obtain OAuth access token');
        }

        $profile = $this->fetchOAuthProfile($provider, $token);
        if ($profile === null || empty($profile['email'])) {
            return redirect()->to('/login')->with('error', 'Unable to read email from OAuth provider');
        }

        $user = $this->userModel->where('email', $profile['email'])->first();
        if (! $user) {
            $createData = [
                'name' => $profile['name'] ?: $profile['email'],
                'email' => $profile['email'],
                'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'avatar' => $profile['avatar'],
            ];
            if ($provider === 'google') {
                $createData['google_id'] = $profile['provider_id'];
            } elseif ($provider === 'github') {
                $createData['github_id'] = $profile['provider_id'];
            }

            if (! $this->userModel->skipValidation(true)->insert($createData)) {
                return redirect()->to('/login')->with('error', 'Unable to create social account');
            }

            $userId = $this->userModel->getInsertID();
            $user = $this->userModel->find($userId);
        } else {
            $updateData = [];
            if ($provider === 'google' && empty($user['google_id'])) {
                $updateData['google_id'] = $profile['provider_id'];
            }
            if ($provider === 'github' && empty($user['github_id'])) {
                $updateData['github_id'] = $profile['provider_id'];
            }
            if (! empty($profile['avatar'])) {
                $updateData['avatar'] = $profile['avatar'];
            }
            if (! empty($updateData)) {
                $this->userModel->update($user['id'], $updateData);
                $user = $this->userModel->find($user['id']);
            }
        }

        $this->session->set('user_id', $user['id']);
        $this->session->set('user_name', $user['name']);

        return redirect()->to('/dashboard');
    }

    private function getSocialConfig(string $provider): ?array
    {
        if ($provider === 'google') {
            $clientId = $this->readEnv([
                'oauth.google.client_id',
                'oauth.google.clientId',
                'oauth_google_client_id',
            ]);
            $clientSecret = $this->readEnv([
                'oauth.google.client_secret',
                'oauth.google.clientSecret',
                'oauth_google_client_secret',
            ]);

            if (empty($clientId) || empty($clientSecret)) {
                return null;
            }

            return [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ];
        }

        if ($provider === 'github') {
            $clientId = $this->readEnv([
                'oauth.github.client_id',
                'oauth.github.clientId',
                'oauth_github_client_id',
            ]);
            $clientSecret = $this->readEnv([
                'oauth.github.client_secret',
                'oauth.github.clientSecret',
                'oauth_github_client_secret',
            ]);

            if (empty($clientId) || empty($clientSecret)) {
                return null;
            }

            return [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ];
        }

        return null;
    }

    private function readEnv(array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = env($key);
            if (! empty($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private function exchangeCodeForToken(string $provider, string $code, string $redirectUri, array $config): ?string
    {
        $client = Services::curlrequest(['http_errors' => false]);

        if ($provider === 'google') {
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ],
            ]);
            $data = json_decode((string) $response->getBody(), true);
            return $data['access_token'] ?? null;
        }

        if ($provider === 'github') {
            $response = $client->post('https://github.com/login/oauth/access_token', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'code' => $code,
                    'client_id' => $config['client_id'],
                    'client_secret' => $config['client_secret'],
                    'redirect_uri' => $redirectUri,
                ],
            ]);
            $data = json_decode((string) $response->getBody(), true);
            return $data['access_token'] ?? null;
        }

        return null;
    }

    private function fetchOAuthProfile(string $provider, string $accessToken): ?array
    {
        $client = Services::curlrequest(['http_errors' => false]);

        if ($provider === 'google') {
            $response = $client->get('https://www.googleapis.com/oauth2/v2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);
            $data = json_decode((string) $response->getBody(), true);
            if (empty($data['id']) || empty($data['email'])) {
                return null;
            }

            return [
                'provider_id' => (string) $data['id'],
                'name' => (string) ($data['name'] ?? ''),
                'email' => (string) $data['email'],
                'avatar' => (string) ($data['picture'] ?? ''),
            ];
        }

        if ($provider === 'github') {
            $userResponse = $client->get('https://api.github.com/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/vnd.github+json',
                    'User-Agent' => 'ci4-social-auth',
                ],
            ]);
            $userData = json_decode((string) $userResponse->getBody(), true);
            if (empty($userData['id'])) {
                return null;
            }

            $email = $userData['email'] ?? null;
            if (empty($email)) {
                $emailsResponse = $client->get('https://api.github.com/user/emails', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Accept' => 'application/vnd.github+json',
                        'User-Agent' => 'ci4-social-auth',
                    ],
                ]);
                $emailsData = json_decode((string) $emailsResponse->getBody(), true);
                if (is_array($emailsData)) {
                    foreach ($emailsData as $item) {
                        if (! empty($item['primary']) && ! empty($item['verified']) && ! empty($item['email'])) {
                            $email = $item['email'];
                            break;
                        }
                    }
                    if (empty($email)) {
                        foreach ($emailsData as $item) {
                            if (! empty($item['email'])) {
                                $email = $item['email'];
                                break;
                            }
                        }
                    }
                }
            }

            if (empty($email)) {
                return null;
            }

            return [
                'provider_id' => (string) $userData['id'],
                'name' => (string) ($userData['name'] ?? $userData['login'] ?? ''),
                'email' => (string) $email,
                'avatar' => (string) ($userData['avatar_url'] ?? ''),
            ];
        }

        return null;
    }
}
