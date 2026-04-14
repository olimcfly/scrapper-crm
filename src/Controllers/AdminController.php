<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\AdminModules;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

final class AdminController
{
    public function dashboard(Request $request): void
    {
        $query = $request->input();
        $state = strtolower((string) ($query['state'] ?? ''));

        View::render('admin/dashboard', [
            'title' => 'Tableau de bord',
            'modules' => AdminModules::all(),
            'coreModules' => AdminModules::coreModules(),
            'statusCounters' => AdminModules::statusCounters(),
            'statusLabels' => AdminModules::statusLabels(),
            'statusClassMap' => AdminModules::statusClassMap(),
            'showLoading' => $state === 'loading',
            'showEmpty' => $state === 'empty',
            'showError' => $state === 'error',
        ]);
    }

    public function moduleAlias(Request $request, string $moduleKey): void
    {
        $this->module($request, $moduleKey);
    }

    public function module(Request $request, string $moduleKey): void
    {
        unset($request);

        $module = AdminModules::findByKey($moduleKey);
        if ($module === null) {
            Response::json(['error' => 'Module introuvable'], 404);
            return;
        }

        View::render('admin/module', [
            'title' => $module['label'],
            'module' => $module,
            'statusLabels' => AdminModules::statusLabels(),
            'statusClassMap' => AdminModules::statusClassMap(),
        ]);
    }
}
