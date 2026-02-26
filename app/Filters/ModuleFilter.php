<?php

namespace App\Filters;

use App\Libraries\ModuleAccess;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ModuleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        if (empty($arguments) || ! is_array($arguments)) {
            return;
        }

        $moduleKey = (string) $arguments[0];
        if ($moduleKey === '') {
            return;
        }

        $canAccess = (new ModuleAccess())->canAccess($userId, $moduleKey);
        if (! $canAccess) {
            return redirect()->to('/dashboard')->with('error', lang('App.access_denied'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
