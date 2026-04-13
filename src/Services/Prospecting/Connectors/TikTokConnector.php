<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

final class TikTokConnector extends OfficialApiPlaceholderConnector
{
    protected static function sourceKey(): string
    {
        return 'tiktok';
    }
}
