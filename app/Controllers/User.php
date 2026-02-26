<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ModuleAccess;
use App\Models\RoleModel;
use App\Models\UserModel;

class User extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected ModuleAccess $moduleAccess;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->moduleAccess = new ModuleAccess();
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $search = trim((string) $this->request->getGet('q'));
        $role = trim((string) $this->request->getGet('role'));
        $validRoleSlugs = array_column($this->roleModel->findAll(), 'slug');

        $builder = $this->userModel
            ->select('id, name, email, role, created_at')
            ->orderBy('id', 'DESC');

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if (in_array($role, $validRoleSlugs, true)) {
            $builder->where('role', $role);
        }

        $users = $builder->paginate(10);

        return view('users/index', [
            'title' => lang('App.users'),
            'users' => $users,
            'pager' => $this->userModel->pager,
            'search' => $search,
            'roleFilter' => $role,
            'roles' => $this->roleModel->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $roles = $this->roleModel->orderBy('name', 'ASC')->findAll();
        $selectedRole = old('role', 'user');
        $roleModules = $this->moduleAccess->getRoleVisibleModuleKeys((string) $selectedRole);

        return view('users/create', [
            'title' => lang('App.create_user'),
            'roles' => $roles,
            'modules' => $this->moduleAccess->getModuleDefinitions(),
            'selectedModules' => $roleModules,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/users');
        }

        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
            'password' => (string) $this->request->getPost('password'),
            'role' => trim((string) $this->request->getPost('role')),
        ];

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|alpha_dash|min_length[3]|max_length[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! $this->roleExists($data['role'])) {
            return redirect()->back()->withInput()->with('error', lang('App.invalid_role_selected'));
        }

        $saveData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
        ];

        if (! $this->userModel->skipValidation(true)->insert($saveData)) {
            $errors = $this->userModel->errors() ?: [lang('App.unable_to_save_user')];
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $userId = (int) $this->userModel->getInsertID();
        $modules = (array) ($this->request->getPost('modules') ?? []);
        $this->moduleAccess->saveUserOverrides($userId, $modules);

        return redirect()->to('/users')->with('success', lang('App.user_created_successfully'));
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $user = $this->userModel->find((int) $id);
        if (! $user) {
            return redirect()->to('/users')->with('error', lang('App.user_not_found'));
        }

        $visibility = $this->moduleAccess->getVisibilityMapForUser((int) $user['id']);
        $selectedModules = array_keys(array_filter($visibility, static fn(bool $show): bool => $show));

        return view('users/edit', [
            'title' => lang('App.edit_user'),
            'user' => $user,
            'roles' => $this->roleModel->orderBy('name', 'ASC')->findAll(),
            'modules' => $this->moduleAccess->getModuleDefinitions(),
            'selectedModules' => $selectedModules,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/users');
        }

        $userId = (int) $id;
        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/users')->with('error', lang('App.user_not_found'));
        }

        $password = (string) $this->request->getPost('password');
        $newRole = trim((string) $this->request->getPost('role'));

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'role' => 'required|alpha_dash|min_length[3]|max_length[50]',
        ];

        if ($password !== '') {
            $rules['password'] = 'min_length[6]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (! $this->roleExists($newRole)) {
            return redirect()->back()->withInput()->with('error', lang('App.invalid_role_selected'));
        }

        $updateData = [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
            'role' => $newRole,
        ];

        if ($password !== '') {
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (! $this->userModel->skipValidation(true)->update($userId, $updateData)) {
            $errors = $this->userModel->errors() ?: [lang('App.unable_to_save_user')];
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $modules = (array) ($this->request->getPost('modules') ?? []);
        $this->moduleAccess->saveUserOverrides($userId, $modules);

        if ((int) session()->get('user_id') === $userId) {
            session()->set('user_name', $updateData['name']);
            session()->set('user_role', $updateData['role']);
        }

        return redirect()->to('/users')->with('success', lang('App.user_updated_successfully'));
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $userId = (int) $id;
        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to('/users')->with('error', lang('App.user_not_found'));
        }

        if ($userId === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', lang('App.cannot_delete_current_user'));
        }

        $this->userModel->delete($userId);
        return redirect()->to('/users')->with('success', lang('App.user_deleted_successfully'));
    }

    private function ensureAdmin()
    {
        if (! session()->get('user_id')) {
            return redirect()->to('/login');
        }

        if ((string) session()->get('user_role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', lang('App.access_denied'));
        }

        return null;
    }

    private function roleExists(string $slug): bool
    {
        if ($slug === '') {
            return false;
        }

        return $this->roleModel->where('slug', $slug)->first() !== null;
    }
}
