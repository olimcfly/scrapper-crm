<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\ProspectModel;
use RuntimeException;

final class CsvProspectImportService
{
    private ProspectModel $prospects;
    private ProspectValidator $validator;

    public function __construct()
    {
        $this->prospects = new ProspectModel();
        $this->validator = new ProspectValidator();
    }

    /**
     * @param array<string, mixed> $file
     * @return array{headers: array<int, string>, rows: array<int, array<int, string>>, file_name: string}
     */
    public function parseUploadedFile(array $file): array
    {
        $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Le fichier CSV est manquant ou invalide.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? 'import.csv');
        if ($tmpPath === '' || !is_file($tmpPath)) {
            throw new RuntimeException('Impossible de lire le fichier uploadé.');
        }

        $handle = fopen($tmpPath, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Impossible d’ouvrir le fichier CSV.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new RuntimeException('Le fichier CSV est vide.');
        }

        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $rawRows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($row === [null] || $row === []) {
                continue;
            }

            $normalized = array_map(static fn ($value): string => trim((string) $value), $row);
            if (implode('', $normalized) === '') {
                continue;
            }

            $rawRows[] = $normalized;
        }

        fclose($handle);

        if ($rawRows === []) {
            throw new RuntimeException('Le fichier CSV est vide.');
        }

        $headers = $rawRows[0];
        $rows = array_slice($rawRows, 1);

        if ($rows === []) {
            throw new RuntimeException('Aucune ligne de données à importer.');
        }

        $headers = $this->normalizeHeaders($headers);
        $rows = $this->normalizeRows($rows, count($headers));

        return [
            'headers' => $headers,
            'rows' => $rows,
            'file_name' => $originalName,
        ];
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, string>> $rows
     * @param array<string, string> $mapping
     * @return array{success_count: int, error_count: int, errors: array<int, string>}
     */
    public function import(array $headers, array $rows, array $mapping): array
    {
        $requiredMappings = ['first_name', 'last_name'];
        foreach ($requiredMappings as $field) {
            if (!isset($mapping[$field]) || $mapping[$field] === '') {
                throw new RuntimeException('Le mapping des colonnes prénom et nom est requis.');
            }
        }

        $successCount = 0;
        $errors = [];

        Database::connection()->beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $payload = $this->buildPayload($headers, $row, $mapping);
                $validationErrors = $this->validator->validate($payload);

                if ($validationErrors !== []) {
                    $errors[] = 'Ligne ' . $rowNumber . ' invalide : ' . implode(' ', $validationErrors);
                    continue;
                }

                $normalized = $this->validator->normalize($payload);

                try {
                    $this->prospects->create($normalized);
                    $successCount++;
                } catch (\Throwable $e) {
                    $errors[] = 'Ligne ' . $rowNumber . ' invalide : erreur base de données.';
                }
            }

            Database::connection()->commit();
        } catch (\Throwable $e) {
            Database::connection()->rollBack();
            throw new RuntimeException('Import interrompu : ' . $e->getMessage());
        }

        return [
            'success_count' => $successCount,
            'error_count' => count($errors),
            'errors' => $errors,
        ];
    }

    /** @param array<int, string> $headers */
    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];

        foreach ($headers as $index => $header) {
            $label = trim($header);
            if ($label === '') {
                $label = 'Colonne ' . ($index + 1);
            }

            $normalized[] = $label;
        }

        return $normalized;
    }

    /**
     * @param array<int, array<int, string>> $rows
     * @return array<int, array<int, string>>
     */
    private function normalizeRows(array $rows, int $headerCount): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $row = array_pad($row, $headerCount, '');
            $normalized[] = array_slice($row, 0, $headerCount);
        }

        return $normalized;
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, string> $row
     * @param array<string, string> $mapping
     * @return array<string, mixed>
     */
    private function buildPayload(array $headers, array $row, array $mapping): array
    {
        $headerIndexByName = [];
        foreach ($headers as $index => $header) {
            $headerIndexByName[$header] = $index;
        }

        $payload = [];

        foreach ($mapping as $targetField => $sourceHeader) {
            if ($sourceHeader === '' || !isset($headerIndexByName[$sourceHeader])) {
                continue;
            }

            $columnIndex = $headerIndexByName[$sourceHeader];
            $payload[$targetField] = $row[$columnIndex] ?? '';
        }

        return $payload;
    }
}
