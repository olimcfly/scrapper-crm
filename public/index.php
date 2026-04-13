<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ApifyController;
use App\Controllers\AuthController;
use App\Controllers\ContentController;
use App\Controllers\GeneratedContentController;
use App\Controllers\LookupController;
use App\Controllers\MessagesController;
use App\Controllers\PipelineController;
use App\Controllers\ProspectingController;
use App\Controllers\ProspectController;
use App\Controllers\SettingsController;
use App\Controllers\StrategyController;
use App\Controllers\WebProspectController;
use App\Core\AuthGuard;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Services\Auth;
use App\Services\Logger;

require dirname(__DIR__) . '/src/Core/bootstrap.php';

$appConfig = require dirname(__DIR__) . '/config/app.php';
$allowedOrigins = $appConfig['cors']['allowed_origins'] ?? [];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (is_string($origin) && $origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    if ($origin !== '' && !in_array($origin, $allowedOrigins, true)) {
        http_response_code(403);
        exit;
    }

    http_response_code(204);
    exit;
}

$request = new Request();
$router = new Router();
$adminController = new AdminController();
$settingsController = new SettingsController();
$pipelineController = new PipelineController();

$guard = new AuthGuard(new Auth(Database::connection()));

$router->add('GET', '/', static function (): void {
    Response::redirect('/dashboard');
});

$router->add('GET', '/login', static fn (Request $req): mixed => (new AuthController())->showLogin($req));
$router->add('POST', '/login', static fn (Request $req): mixed => (new AuthController())->login($req));
$router->add('GET', '/login/verify', static fn (Request $req): mixed => (new AuthController())->showVerify($req));
$router->add('POST', '/login/verify', static fn (Request $req): mixed => (new AuthController())->verify($req));
$router->add('POST', '/logout', static fn (Request $req): mixed => (new AuthController())->logout($req));

// Web routes (PHP views)
$router->add('GET', '/prospects', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->index($req)));
$router->add('GET', '/prospects/create', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->create($req)));
$router->add('POST', '/prospects/create', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->store($req)));
// Routes statiques avant les routes paramétrées (évite que /prospects/{id} capture "import")
$router->add('GET', '/prospects/import', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->importForm($req)));
$router->add('GET', '/prospects/sources', $guard->protect(static fn (Request $req): mixed => (new ProspectingController())->index($req)));
$router->add('POST', '/prospects/import/upload', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->importUpload($req)));
$router->add('POST', '/prospects/import/process', $guard->protect(static fn (Request $req): mixed => (new WebProspectController())->importProcess($req)));
$router->add('GET', '/prospects/{id}', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->show($req, (int) $params['id'])));
$router->add('GET', '/prospects/{id}/edit', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->edit($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/edit', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->update($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/delete', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->destroy($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/notes', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->addNote($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/status', $guard->protect(static fn (Request $req, array $params): mixed => (new WebProspectController())->changeStatus($req, (int) $params['id'])));
$router->add('GET', '/prospects/{id}/generated-contents', $guard->protect(static fn (Request $req, array $params): mixed => (new GeneratedContentController())->create($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/generated-contents/generate', $guard->protect(static fn (Request $req, array $params): mixed => (new GeneratedContentController())->generate($req, (int) $params['id'])));
$router->add('GET', '/settings', $guard->protect(static fn (Request $req): mixed => (new SettingsController())->index($req)));

$router->add('GET', '/dashboard', $guard->protect(static fn (Request $req): mixed => $adminController->dashboard($req)));
$router->add('GET', '/strategie', $guard->protect(static fn (Request $req): mixed => (new StrategyController())->index($req)));
$router->add('POST', '/strategie/analyse', $guard->protect(static fn (Request $req): mixed => (new StrategyController())->analyze($req)));
$router->add('POST', '/strategie/vers-contenu', $guard->protect(static fn (Request $req): mixed => (new StrategyController())->bridgeToContent($req)));
$router->add('GET', '/contenu', $guard->protect(static fn (Request $req): mixed => (new ContentController())->index($req)));
$router->add('POST', '/contenu/generer', $guard->protect(static fn (Request $req): mixed => (new ContentController())->generate($req)));
$router->add('POST', '/contenu/dupliquer', $guard->protect(static fn (Request $req): mixed => (new ContentController())->duplicateDraft($req)));
$router->add('GET', '/messages-ia', $guard->protect(static fn (Request $req): mixed => (new MessagesController())->index($req)));
$router->add('POST', '/messages-ia/generer', $guard->protect(static fn (Request $req): mixed => (new MessagesController())->generate($req)));
$router->add('POST', '/messages-ia/dupliquer', $guard->protect(static fn (Request $req): mixed => (new MessagesController())->duplicateDraft($req)));
$router->add('GET', '/pipeline', $guard->protect(static fn (Request $req): mixed => $pipelineController->index($req)));
$router->add('POST', '/pipeline/{id}/move', $guard->protect(static fn (Request $req, array $params): mixed => $pipelineController->moveStage($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/messages', $guard->protect(static fn (Request $req, array $params): mixed => $pipelineController->addMessage($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/suggest-next-action', $guard->protect(static fn (Request $req, array $params): mixed => $pipelineController->suggest($req, (int) $params['id'])));
$router->add('GET', '/admin', $guard->protect(static function (): void {
    Response::redirect('/dashboard');
}));
$router->add('GET', '/admin/dashboard', $guard->protect(static fn (Request $req): mixed => $adminController->dashboard($req)));
$router->add('GET', '/admin/modules/generation-contenu', $guard->protect(static function (): void {
    Response::redirect('/contenu');
}));
$router->add('GET', '/admin/modules/{module}', $guard->protect(static fn (Request $req, array $params): mixed => $adminController->module($req, (string) $params['module'])));

$router->add('GET', '/strategie', $guard->protect(static fn (Request $req): mixed => $adminController->moduleAlias($req, 'strategie-prospect')));
$router->add('GET', '/messages-ia', $guard->protect(static fn (Request $req): mixed => (new MessagesController())->index($req)));
$router->add('GET', '/parametres', $guard->protect(static fn (Request $req): mixed => $settingsController->index($req)));

// API routes (JSON)
$router->add('GET', '/api/health', static function (): void {
    Response::json(['status' => 'ok']);
});
$router->add('GET', '/api/prospects', static fn (Request $req): mixed => (new ProspectController())->index($req));
$router->add('GET', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => (new ProspectController())->show($req, (int) $params['id']));
$router->add('POST', '/api/prospects', static fn (Request $req): mixed => (new ProspectController())->store($req));
$router->add('PUT', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => (new ProspectController())->update($req, (int) $params['id']));
$router->add('DELETE', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => (new ProspectController())->delete($req, (int) $params['id']));
$router->add('GET', '/api/prospects/{id}/notes', static fn (Request $req, array $params): mixed => (new ProspectController())->notes($req, (int) $params['id']));
$router->add('POST', '/api/prospects/{id}/notes', static fn (Request $req, array $params): mixed => (new ProspectController())->addNote($req, (int) $params['id']));
$router->add('PATCH', '/api/prospects/{id}/status', static fn (Request $req, array $params): mixed => (new ProspectController())->changeStatus($req, (int) $params['id']));
$router->add('GET', '/api/prospect-statuses', static fn (Request $req): mixed => (new LookupController())->statuses($req));
$router->add('GET', '/api/sources', static fn (Request $req): mixed => (new LookupController())->sources($req));
$router->add('GET', '/api/tags', static fn (Request $req): mixed => (new LookupController())->tags($req));
$router->add('GET', '/api/prospecting/sources', static fn (Request $req): mixed => (new ProspectingController())->sources($req));
$router->add('POST', '/api/prospecting/connect/test', static fn (Request $req): mixed => (new ProspectingController())->testConnection($req));
$router->add('POST', '/api/prospecting/search', static fn (Request $req): mixed => (new ProspectingController())->runSearch($req));
$router->add('POST', '/api/apify/run/google-maps', static fn (Request $req): mixed => (new ApifyController())->runSource($req, 'google_maps'));
$router->add('POST', '/api/apify/run/instagram-profile', static fn (Request $req): mixed => (new ApifyController())->runSource($req, 'instagram_profile'));
$router->add('POST', '/api/apify/run/instagram-hashtag', static fn (Request $req): mixed => (new ApifyController())->runSource($req, 'instagram_hashtag'));
$router->add('POST', '/api/apify/run/linkedin-profile', static fn (Request $req): mixed => (new ApifyController())->runSource($req, 'linkedin_profile'));
$router->add('POST', '/api/apify/run/tiktok', static fn (Request $req): mixed => (new ApifyController())->runSource($req, 'tiktok'));
$router->add('GET', '/api/apify/runs/{runId}', static fn (Request $req, array $params): mixed => (new ApifyController())->getRun($req, (string) $params['runId']));
$router->add('GET', '/api/apify/datasets/{datasetId}', static fn (Request $req, array $params): mixed => (new ApifyController())->getDataset($req, (string) $params['datasetId']));
$router->add('POST', '/api/import/google-maps', static fn (Request $req): mixed => (new ApifyController())->importFromSource($req, 'google_maps'));
$router->add('POST', '/api/import/instagram-profile', static fn (Request $req): mixed => (new ApifyController())->importFromSource($req, 'instagram_profile'));
$router->add('POST', '/api/import/linkedin-profile', static fn (Request $req): mixed => (new ApifyController())->importFromSource($req, 'linkedin_profile'));
$router->add('POST', '/api/import/tiktok', static fn (Request $req): mixed => (new ApifyController())->importFromSource($req, 'tiktok'));

try {
    $router->dispatch($request);
} catch (Throwable $e) {
    Logger::error('Unhandled exception: ' . $e->getMessage());
    Response::json(['error' => 'Erreur serveur interne.'], 500);
}
