<?php

use App\Libraries\ModuleAccess;

if (! function_exists('app_modules')) {
    /**
     * @return array<string, array<string, string|int>>
     */
    function app_modules(): array
    {
        return (new ModuleAccess())->getModuleDefinitions();
    }
}

if (! function_exists('current_user_visible_modules')) {
    /**
     * @return list<string>
     */
    function current_user_visible_modules(): array
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return [];
        }

        return (new ModuleAccess())->getVisibleModuleKeysForUser($userId);
    }
}
