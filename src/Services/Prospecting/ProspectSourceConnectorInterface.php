<?php

declare(strict_types=1);

namespace App\Services\Prospecting;

interface ProspectSourceConnectorInterface
{
    public function search(array $filters): array;

    public function connect(array $credentials): array;

    public function sync(array $payload = []): array;

    public function normalize(array $item): array;
}
