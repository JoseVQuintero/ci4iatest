<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('user_id')) {
            return redirect()->to('/login');
        }

        if (empty($arguments) || ! is_array($arguments)) {
            return;
        }

        $role = (string) session()->get('user_role');

        if ($role === '') {
            $user = (new UserModel())->find((int) session()->get('user_id'));
            $role = (string) ($user['role'] ?? 'user');
            session()->set('user_role', $role);
        }

        if (! in_array($role, $arguments, true)) {
            return redirect()->to('/dashboard')->with('error', lang('App.access_denied'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
