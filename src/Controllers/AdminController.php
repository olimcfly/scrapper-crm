<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\AdminModules;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ProspectModel;
use App\Models\ProspectStatusModel;
use App\Models\SourceModel;

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
        $module = AdminModules::findByKey($moduleKey);
        if ($module === null) {
            Response::json(['error' => 'Module introuvable'], 404);
            return;
        }

        if ($moduleKey === 'contacts') {
            $input = $request->input();
            $page = max(1, (int) ($input['page'] ?? 1));
            $filters = [
                'q' => trim((string) ($input['q'] ?? '')),
                'source_id' => (int) ($input['source_id'] ?? 0),
                'status_id' => (int) ($input['status_id'] ?? 0),
                'city' => trim((string) ($input['city'] ?? '')),
                'has_email' => (int) ($input['has_email'] ?? 0),
                'has_phone' => (int) ($input['has_phone'] ?? 0),
            ];

            $prospects = (new ProspectModel())->search($filters, $page, 20);
            $allProspects = (new ProspectModel())->search([], 1, 10000)['items'];

            $stats = ['total' => count($allProspects), 'new' => 0, 'contacted' => 0, 'clients' => 0];
            foreach ($allProspects as $item) {
                $status = mb_strtolower((string) ($item['status_name'] ?? ''));
                if (str_contains($status, 'new') || str_contains($status, 'nouveau') || str_contains($status, 'lead')) {
                    $stats['new']++;
                } elseif (str_contains($status, 'contact')) {
                    $stats['contacted']++;
                } elseif (str_contains($status, 'client')) {
                    $stats['clients']++;
                }
            }

            View::render('admin/contacts', [
                'title' => 'Contacts',
                'module' => $module,
                'contacts' => $prospects['items'],
                'filters' => $filters,
                'pagination' => ['page' => $prospects['page'], 'total_pages' => $prospects['total_pages']],
                'stats' => $stats,
                'sources' => (new SourceModel())->all(),
                'statuses' => (new ProspectStatusModel())->all(),
            ]);
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
