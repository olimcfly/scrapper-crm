<?php

declare(strict_types=1);

namespace App\Services\Prospecting;

use RuntimeException;

final class ConnectorRegistry
{
    /** @var array<string, array<string, mixed>> */
    private array $sources;

    public function __construct()
    {
        $config = require dirname(__DIR__, 3) . '/config/prospecting.php';
        $this->sources = is_array($config['sources'] ?? null) ? $config['sources'] : [];
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return $this->sources;
    }

    public function connector(string $source): ProspectSourceConnectorInterface
    {
        $sourceConfig = $this->sources[$source] ?? null;
        if (!is_array($sourceConfig)) {
            throw new RuntimeException('Source inconnue: ' . $source);
        }

        $className = (string) ($sourceConfig['connector'] ?? '');
        if ($className === '' || !class_exists($className)) {
            throw new RuntimeException('Connecteur non disponible pour la source: ' . $source);
        }

        $connector = new $className();
        if (!$connector instanceof ProspectSourceConnectorInterface) {
            throw new RuntimeException('Le connecteur doit implémenter ProspectSourceConnectorInterface.');
        }

        return $connector;
    }
}
