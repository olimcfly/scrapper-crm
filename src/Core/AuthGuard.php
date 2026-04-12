<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\Auth;

final class AuthGuard
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function protect(callable $handler): callable
    {
        return function (Request $request, array $params = []) use ($handler): mixed {
            if (!$this->auth->check()) {
                Response::redirect('/login');
            }

            return $handler($request, $params);
        };
    }
}
