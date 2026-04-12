<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Support\AdminModules;

final class AdminController
{
    public function dashboard(Request $request): void
    {
        unset($request);

        View::render('admin/dashboard', [
            'title' => 'Admin premium',
            'modules' => AdminModules::all(),
            'coreModules' => AdminModules::coreModules(),
            'statusCounts' => AdminModules::statusCounts(),
        ]);
    }

    public function module(Request $request, string $slug): void
    {
        unset($request);

        $module = AdminModules::findBySlug($slug);
        if ($module === null) {
            View::render('errors/not-found', ['title' => 'Module introuvable']);
            return;
        }

        $isPlaceholderRoute = str_starts_with($module['route'], '/admin/modules/');

        View::render('admin/module_placeholder', [
            'title' => $module['label'],
            'module' => $module,
            'isPlaceholderRoute' => $isPlaceholderRoute,
            'coreModules' => AdminModules::coreModules(),
        ]);
    }
}
