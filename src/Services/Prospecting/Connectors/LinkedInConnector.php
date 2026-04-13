<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

final class LinkedInConnector extends OfficialApiPlaceholderConnector
{
    protected static function sourceKey(): string
    {
        return 'linkedin';
    }
}
