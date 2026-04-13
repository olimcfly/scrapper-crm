<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ApifyController;
use App\Controllers\AuthController;
use App\Controllers\ContentController;
use App\Controllers\MessagesController;
use App\Controllers\PipelineController;
use App\Controllers\ProspectingController;
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

/*
|--------------------------------------------------------------------------
| REDIRECT ROOT
|--------------------------------------------------------------------------
*/
$router->add('GET', '/', static function (): void {
    Response::redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
$router->add('GET', '/login', fn (Request $req) => (new AuthController())->showLogin($req));
$router->add('POST', '/login', fn (Request $req) => (new AuthController())->login($req));
$router->add('GET', '/login/verify', fn (Request $req) => (new AuthController())->showVerify($req));
$router->add('POST', '/login/verify', fn (Request $req) => (new AuthController())->verify($req));
$router->add('POST', '/logout', fn (Request $req) => (new AuthController())->logout($req));

/*
|--------------------------------------------------------------------------
| PROSPECTS (UI)
|--------------------------------------------------------------------------
*/
$router->add('GET', '/prospects', $guard->protect(fn (Request $req) => (new WebProspectController())->index($req)));

$router->add('GET', '/prospects/sources', $guard->protect(fn (Request $req) => (new ProspectingController())->sources($req)));

$router->add('GET', '/prospects/create', $guard->protect(fn (Request $req) => (new WebProspectController())->create($req)));
$router->add('POST', '/prospects/create', $guard->protect(fn (Request $req) => (new WebProspectController())->store($req)));

$router->add('GET', '/prospects/import', $guard->protect(fn (Request $req) => (new WebProspectController())->importForm($req)));
$router->add('POST', '/prospects/import/upload', $guard->protect(fn (Request $req) => (new WebProspectController())->importUpload($req)));
$router->add('POST', '/prospects/import/process', $guard->protect(fn (Request $req) => (new WebProspectController())->importProcess($req)));

$router->add('GET', '/prospects/{id}', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->show($req, (int) $params['id'])));
$router->add('GET', '/prospects/{id}/edit', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->edit($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/edit', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->update($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/delete', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->destroy($req, (int) $params['id'])));

/*
|--------------------------------------------------------------------------
| CONTENU
|--------------------------------------------------------------------------
*/
$router->add('GET', '/contenu', $guard->protect(fn (Request $req) => (new ContentController())->index($req)));
$router->add('POST', '/contenu/generer', $guard->protect(fn (Request $req) => (new ContentController())->generate($req)));
$router->add('POST', '/contenu/dupliquer', $guard->protect(fn (Request $req) => (new ContentController())->duplicateDraft($req)));

/*
|--------------------------------------------------------------------------
| AUTRES MODULES
|--------------------------------------------------------------------------
*/
$router->add('GET', '/dashboard', $guard->protect(fn (Request $req) => $adminController->dashboard($req)));
$router->add('GET', '/admin', $guard->protect(static function (): void {
    Response::redirect('/dashboard');
}));

$router->add('GET', '/strategie', $guard->protect(fn (Request $req) => (new StrategyController())->index($req)));
$router->add('POST', '/strategie/analyse', $guard->protect(fn (Request $req) => (new StrategyController())->analyze($req)));

$router->add('GET', '/messages-ia', $guard->protect(fn (Request $req) => (new MessagesController())->index($req)));
$router->add('POST', '/messages-ia/generer', $guard->protect(fn (Request $req) => (new MessagesController())->generate($req)));
$router->add('POST', '/messages-ia/dupliquer', $guard->protect(fn (Request $req) => (new MessagesController())->duplicateDraft($req)));
$router->add('POST', '/messages-ia/generate', $guard->protect(fn (Request $req) => (new MessagesController())->generate($req)));

$router->add('GET', '/pipeline', $guard->protect(fn (Request $req) => $pipelineController->index($req)));
$router->add('POST', '/pipeline/{id}/move', $guard->protect(fn (Request $req, array $params) => $pipelineController->moveStage($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/messages', $guard->protect(fn (Request $req, array $params) => $pipelineController->addMessage($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/suggest-next-action', $guard->protect(fn (Request $req, array $params) => $pipelineController->suggest($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/notes', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->addNote($req, (int) $params['id'])));
$router->add('POST', '/prospects/{id}/status', $guard->protect(fn (Request $req, array $params) => (new WebProspectController())->changeStatus($req, (int) $params['id'])));

$router->add('GET', '/settings', $guard->protect(fn (Request $req) => $settingsController->index($req)));
$router->add('GET', '/admin/modules/{moduleKey}', $guard->protect(fn (Request $req, array $params) => $adminController->moduleAlias($req, (string) $params['moduleKey'])));

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
$router->add('GET', '/api/health', fn () => Response::json(['status' => 'ok']));

$router->add('POST', '/api/prospecting/connect/test', fn (Request $req) => (new ProspectingController())->testConnection($req));
$router->add('POST', '/api/prospecting/search', fn (Request $req) => (new ProspectingController())->runSearch($req));

/*
|--------------------------------------------------------------------------
| DISPATCH
|--------------------------------------------------------------------------
*/
try {
    $router->dispatch($request);
} catch (Throwable $e) {
    Logger::error('Unhandled exception: ' . $e->getMessage());
    Response::json(['error' => 'Erreur serveur interne.'], 500);
}
