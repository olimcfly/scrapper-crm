<?php

declare(strict_types=1);

namespace App\Services\Prospecting\Connectors;

final class GoogleBusinessProfileConnector extends OfficialApiPlaceholderConnector
{
    protected static function sourceKey(): string
    {
        return 'google_business_profile';
    }
}
