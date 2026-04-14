<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

final class TrainingController
{
    public function index(Request $request): void
    {
        unset($request);
        View::render('training/index', ['title' => 'Formation']);
    }
}
