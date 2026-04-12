<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\LookupController;
use App\Controllers\ProspectController;
use App\Controllers\WebProspectController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Services\Logger;

require dirname(__DIR__) . '/src/Core/bootstrap.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$request = new Request();
$router = new Router();

$apiProspects = new ProspectController();
$apiLookup = new LookupController();
$webProspects = new WebProspectController();
$authController = new AuthController();

$router->add('GET', '/', static function (): void {
    Response::redirect('/prospects');
});

$router->add('GET', '/login', static fn (Request $req): mixed => $authController->showLogin($req));
$router->add('POST', '/login', static fn (Request $req): mixed => $authController->login($req));
$router->add('POST', '/logout', static fn (Request $req): mixed => $authController->logout($req));

// Web routes (PHP views)
$router->add('GET', '/prospects', static fn (Request $req): mixed => $webProspects->index($req));
$router->add('GET', '/prospects/create', static fn (Request $req): mixed => $webProspects->create($req));
$router->add('POST', '/prospects/create', static fn (Request $req): mixed => $webProspects->store($req));
$router->add('GET', '/prospects/{id}', static fn (Request $req, array $params): mixed => $webProspects->show($req, (int) $params['id']));
$router->add('GET', '/prospects/{id}/edit', static fn (Request $req, array $params): mixed => $webProspects->edit($req, (int) $params['id']));
$router->add('POST', '/prospects/{id}/edit', static fn (Request $req, array $params): mixed => $webProspects->update($req, (int) $params['id']));
$router->add('POST', '/prospects/{id}/delete', static fn (Request $req, array $params): mixed => $webProspects->destroy($req, (int) $params['id']));
$router->add('POST', '/prospects/{id}/notes', static fn (Request $req, array $params): mixed => $webProspects->addNote($req, (int) $params['id']));
$router->add('POST', '/prospects/{id}/status', static fn (Request $req, array $params): mixed => $webProspects->changeStatus($req, (int) $params['id']));

// API routes (JSON)
$router->add('GET', '/api/health', static function (): void {
    Response::json(['status' => 'ok']);
});
$router->add('GET', '/api/prospects', static fn (Request $req): mixed => $apiProspects->index($req));
$router->add('GET', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => $apiProspects->show($req, (int) $params['id']));
$router->add('POST', '/api/prospects', static fn (Request $req): mixed => $apiProspects->store($req));
$router->add('PUT', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => $apiProspects->update($req, (int) $params['id']));
$router->add('DELETE', '/api/prospects/{id}', static fn (Request $req, array $params): mixed => $apiProspects->delete($req, (int) $params['id']));
$router->add('GET', '/api/prospects/{id}/notes', static fn (Request $req, array $params): mixed => $apiProspects->notes($req, (int) $params['id']));
$router->add('POST', '/api/prospects/{id}/notes', static fn (Request $req, array $params): mixed => $apiProspects->addNote($req, (int) $params['id']));
$router->add('PATCH', '/api/prospects/{id}/status', static fn (Request $req, array $params): mixed => $apiProspects->changeStatus($req, (int) $params['id']));
$router->add('GET', '/api/prospect-statuses', static fn (Request $req): mixed => $apiLookup->statuses($req));
$router->add('GET', '/api/sources', static fn (Request $req): mixed => $apiLookup->sources($req));
$router->add('GET', '/api/tags', static fn (Request $req): mixed => $apiLookup->tags($req));

try {
    $router->dispatch($request);
} catch (Throwable $e) {
    Logger::error('Unhandled exception: ' . $e->getMessage());
    Response::json(['error' => 'Erreur serveur interne.'], 500);
}
