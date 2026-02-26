<?php

namespace App\Libraries;

use Config\AppModules;
use Config\Database;

class ModuleAccess
{
    /**
     * @return array<string, array<string, string|int>>
     */
    public function getModuleDefinitions(): array
    {
        $modules = config(AppModules::class)->modules;
        uasort($modules, static fn(array $a, array $b): int => (int) $a['sort'] <=> (int) $b['sort']);
        return $modules;
    }

    /**
     * Effective visibility = role permission + per-user override (hide/show only where role allows).
     *
     * @return array<string, bool>
     */
    public function getVisibilityMapForUser(int $userId): array
    {
        $modules = $this->getModuleDefinitions();
        $visibility = [];
        foreach ($modules as $key => $_meta) {
            $visibility[$key] = false;
        }

        $db = Database::connect();
        $user = $db->table('users')->select('id, role')->where('id', $userId)->get()->getRowArray();
        if (! $user) {
            return $visibility;
        }

        $roleRows = $db->table('role_modules')
            ->select('module_key, is_visible')
            ->where('role_slug', (string) ($user['role'] ?? 'user'))
            ->get()
            ->getResultArray();

        foreach ($roleRows as $row) {
            $key = (string) ($row['module_key'] ?? '');
            if (array_key_exists($key, $visibility)) {
                $visibility[$key] = (int) ($row['is_visible'] ?? 0) === 1;
            }
        }

        $userRows = $db->table('user_modules')
            ->select('module_key, is_visible')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        foreach ($userRows as $row) {
            $key = (string) ($row['module_key'] ?? '');
            if (array_key_exists($key, $visibility) && $visibility[$key] === true) {
                $visibility[$key] = (int) ($row['is_visible'] ?? 0) === 1;
            }
        }

        if (array_key_exists('dashboard', $visibility)) {
            $visibility['dashboard'] = true;
        }

        return $visibility;
    }

    public function canAccess(int $userId, string $moduleKey): bool
    {
        $visibility = $this->getVisibilityMapForUser($userId);
        return (bool) ($visibility[$moduleKey] ?? false);
    }

    /**
     * @return list<string>
     */
    public function getVisibleModuleKeysForUser(int $userId): array
    {
        $visibility = $this->getVisibilityMapForUser($userId);
        return array_values(array_keys(array_filter($visibility, static fn(bool $visible): bool => $visible)));
    }

    /**
     * @param list<string> $visibleKeys
     */
    public function saveUserOverrides(int $userId, array $visibleKeys): void
    {
        $visibleKeySet = array_fill_keys($visibleKeys, true);
        $moduleKeys = array_keys($this->getModuleDefinitions());
        $db = Database::connect();
        $user = $db->table('users')->select('role')->where('id', $userId)->get()->getRowArray();
        $roleSlug = (string) ($user['role'] ?? 'user');

        $roleRows = $db->table('role_modules')
            ->select('module_key, is_visible')
            ->where('role_slug', $roleSlug)
            ->get()
            ->getResultArray();

        $roleVisibility = [];
        foreach ($moduleKeys as $moduleKey) {
            $roleVisibility[$moduleKey] = false;
        }
        foreach ($roleRows as $row) {
            $key = (string) ($row['module_key'] ?? '');
            if (array_key_exists($key, $roleVisibility)) {
                $roleVisibility[$key] = (int) ($row['is_visible'] ?? 0) === 1;
            }
        }

        $db->table('user_modules')->where('user_id', $userId)->delete();
        $now = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($moduleKeys as $moduleKey) {
            if ($moduleKey === 'dashboard') {
                continue;
            }

            if ($roleVisibility[$moduleKey] !== true) {
                continue;
            }

            $isVisible = isset($visibleKeySet[$moduleKey]) ? 1 : 0;
            $rows[] = [
                'user_id' => $userId,
                'module_key' => $moduleKey,
                'is_visible' => $isVisible,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($rows)) {
            $db->table('user_modules')->insertBatch($rows);
        }
    }

    /**
     * @param list<string> $visibleKeys
     */
    public function saveRolePermissions(string $roleSlug, array $visibleKeys): void
    {
        $visibleKeySet = array_fill_keys($visibleKeys, true);
        $moduleKeys = array_keys($this->getModuleDefinitions());
        $db = Database::connect();

        $db->table('role_modules')->where('role_slug', $roleSlug)->delete();
        $now = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($moduleKeys as $moduleKey) {
            $rows[] = [
                'role_slug' => $roleSlug,
                'module_key' => $moduleKey,
                'is_visible' => ($moduleKey === 'dashboard' || isset($visibleKeySet[$moduleKey])) ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $db->table('role_modules')->insertBatch($rows);
    }

    /**
     * @return list<string>
     */
    public function getRoleVisibleModuleKeys(string $roleSlug): array
    {
        $rows = Database::connect()->table('role_modules')
            ->select('module_key')
            ->where('role_slug', $roleSlug)
            ->where('is_visible', 1)
            ->get()
            ->getResultArray();

        $keys = array_map(static fn(array $row): string => (string) $row['module_key'], $rows);
        if (! in_array('dashboard', $keys, true)) {
            $keys[] = 'dashboard';
        }
        return $keys;
    }
}
