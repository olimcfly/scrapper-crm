<?php

declare(strict_types=1);

require __DIR__ . '/src/Core/bootstrap.php';

use App\Services\Mailer;

$result = Mailer::sendOtp('test@coraliemontreuil.fr', '123456');

var_dump($result);
echo PHP_EOL;