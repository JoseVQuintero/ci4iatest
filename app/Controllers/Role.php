<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ModuleAccess;
use App\Models\RoleModel;
use App\Models\UserModel;

class Role extends BaseController
{
    protected RoleModel $roleModel;
    protected ModuleAccess $moduleAccess;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->moduleAccess = new ModuleAccess();
    }

    public function index()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $roles = $this->roleModel->orderBy('name', 'ASC')->findAll();
        $userModel = new UserModel();
        foreach ($roles as &$role) {
            $role['user_count'] = $userModel->where('role', $role['slug'])->countAllResults();
        }
        unset($role);

        return view('roles/index', [
            'title' => lang('App.roles'),
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        return view('roles/create', [
            'title' => lang('App.create_role'),
            'modules' => $this->moduleAccess->getModuleDefinitions(),
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/roles');
        }

        $name = trim((string) $this->request->getPost('name'));
        $slug = trim((string) $this->request->getPost('slug'));
        $description = trim((string) $this->request->getPost('description'));
        $visibleModules = (array) ($this->request->getPost('modules') ?? []);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'slug' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[roles.slug]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $name,
            'slug' => strtolower($slug),
            'description' => $description !== '' ? $description : null,
            'is_system' => 0,
        ];

        if (! $this->roleModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', lang('App.unable_to_save_role'));
        }

        $this->moduleAccess->saveRolePermissions($data['slug'], $visibleModules);

        return redirect()->to('/roles')->with('success', lang('App.role_created_successfully'));
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $role = $this->roleModel->find((int) $id);
        if (! $role) {
            return redirect()->to('/roles')->with('error', lang('App.role_not_found'));
        }

        return view('roles/edit', [
            'title' => lang('App.edit_role'),
            'role' => $role,
            'modules' => $this->moduleAccess->getModuleDefinitions(),
            'roleModules' => $this->moduleAccess->getRoleVisibleModuleKeys($role['slug']),
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/roles');
        }

        $role = $this->roleModel->find((int) $id);
        if (! $role) {
            return redirect()->to('/roles')->with('error', lang('App.role_not_found'));
        }

        $name = trim((string) $this->request->getPost('name'));
        $description = trim((string) $this->request->getPost('description'));
        $visibleModules = (array) ($this->request->getPost('modules') ?? []);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
        ];

        if (! $role['is_system']) {
            $rules['slug'] = 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[roles.slug,id,' . (int) $id . ']';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newSlug = $role['is_system'] ? $role['slug'] : strtolower(trim((string) $this->request->getPost('slug')));
        $this->roleModel->update((int) $id, [
            'name' => $name,
            'slug' => $newSlug,
            'description' => $description !== '' ? $description : null,
        ]);

        if ($newSlug !== $role['slug']) {
            $db = \Config\Database::connect();
            $db->table('users')->where('role', $role['slug'])->set('role', $newSlug)->update();
            $db->table('role_modules')->where('role_slug', $role['slug'])->set('role_slug', $newSlug)->update();
        }

        $this->moduleAccess->saveRolePermissions($newSlug, $visibleModules);

        if ((string) session()->get('user_role') === $role['slug']) {
            session()->set('user_role', $newSlug);
        }

        return redirect()->to('/roles')->with('success', lang('App.role_updated_successfully'));
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAdmin()) {
            return $redirect;
        }

        $role = $this->roleModel->find((int) $id);
        if (! $role) {
            return redirect()->to('/roles')->with('error', lang('App.role_not_found'));
        }

        if ((int) ($role['is_system'] ?? 0) === 1) {
            return redirect()->to('/roles')->with('error', lang('App.cannot_delete_system_role'));
        }

        $usersUsingRole = (new UserModel())->where('role', $role['slug'])->countAllResults();
        if ($usersUsingRole > 0) {
            return redirect()->to('/roles')->with('error', lang('App.cannot_delete_role_in_use'));
        }

        $this->roleModel->delete((int) $id);
        \Config\Database::connect()->table('role_modules')->where('role_slug', $role['slug'])->delete();

        return redirect()->to('/roles')->with('success', lang('App.role_deleted_successfully'));
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
}
