<?php

declare(strict_types=1);

use App\Controllers\LookupController;
use App\Controllers\ProspectController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

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

$prospectController = new ProspectController();
$lookupController = new LookupController();

$router->add('GET', '/health', static function (): void {
    Response::json(['status' => 'ok']);
});

$router->add('GET', '/prospects', static fn (Request $req): mixed => $prospectController->index($req));
$router->add('GET', '/prospects/{id}', static fn (Request $req, array $params): mixed => $prospectController->show($req, (int) $params['id']));
$router->add('POST', '/prospects', static fn (Request $req): mixed => $prospectController->store($req));
$router->add('PUT', '/prospects/{id}', static fn (Request $req, array $params): mixed => $prospectController->update($req, (int) $params['id']));
$router->add('DELETE', '/prospects/{id}', static fn (Request $req, array $params): mixed => $prospectController->delete($req, (int) $params['id']));

$router->add('POST', '/prospects/{id}/notes', static fn (Request $req, array $params): mixed => $prospectController->addNote($req, (int) $params['id']));
$router->add('PATCH', '/prospects/{id}/status', static fn (Request $req, array $params): mixed => $prospectController->changeStatus($req, (int) $params['id']));

$router->add('GET', '/prospect-statuses', static fn (Request $req): mixed => $lookupController->statuses($req));
$router->add('GET', '/sources', static fn (Request $req): mixed => $lookupController->sources($req));
$router->add('GET', '/tags', static fn (Request $req): mixed => $lookupController->tags($req));

try {
    $router->dispatch($request);
} catch (Throwable $e) {
    Response::json(['error' => 'Erreur serveur interne.'], 500);
}
