<?php

declare(strict_types=1);

namespace App\Services;

final class StrategyAnalysisBuilderService
{
    /**
     * @param array<string,mixed> $input
     * @param array<string,mixed> $catalog
     * @return array<string,mixed>
     */
    public function normalizeSelection(array $input, array $catalog): array
    {
        $objective = $this->pickKey($input['objective'] ?? '', $catalog['objectives'] ?? []);
        $personaGroup = $this->pickKey($input['persona_group'] ?? '', $catalog['persona_groups'] ?? []);

        $subtypes = (array) (($catalog['persona_subtypes'] ?? [])[$personaGroup] ?? []);
        $personaSubtype = $this->pickKey($input['persona_subtype'] ?? '', $subtypes);

        $offerType = $this->pickKey($input['offer_type'] ?? '', $catalog['offer_types'] ?? []);
        $maturityLevel = $this->pickKey($input['maturity_level'] ?? '', $catalog['maturity_levels'] ?? []);
        $intention = $this->pickKey($input['contact_intention'] ?? '', $catalog['intentions'] ?? []);
        $quickMode = filter_var($input['quick_mode'] ?? false, FILTER_VALIDATE_BOOL);

        $defaults = (array) (($catalog['defaults_by_persona'] ?? [])[$personaGroup] ?? []);

        if ($quickMode) {
            $offerType = $offerType !== '' ? $offerType : (string) ($defaults['offer_type'] ?? '');
            $maturityLevel = $maturityLevel !== '' ? $maturityLevel : (string) ($defaults['maturity_level'] ?? '');
            $intention = $intention !== '' ? $intention : (string) ($defaults['intention'] ?? '');
            $objective = $objective !== '' ? $objective : (string) ($defaults['objective'] ?? '');
        }

        return [
            'objective' => $objective,
            'persona_group' => $personaGroup,
            'persona_subtype' => $personaSubtype,
            'offer_type' => $offerType,
            'maturity_level' => $maturityLevel,
            'contact_intention' => $intention,
            'quick_mode' => $quickMode,
            'custom_context' => trim((string) ($input['custom_context'] ?? '')),
            'strategy_note' => (string) ($defaults['strategy_note'] ?? ''),
        ];
    }

    /**
     * @param array<string,mixed> $selection
     * @param array<string,mixed> $catalog
     */
    public function buildProfileText(array $selection, array $catalog): string
    {
        $objectiveLabel = $this->labelFor($selection['objective'] ?? '', $catalog['objectives'] ?? []);
        $personaLabel = $this->labelFor($selection['persona_group'] ?? '', $catalog['persona_groups'] ?? []);

        $subtypes = (array) (($catalog['persona_subtypes'] ?? [])[$selection['persona_group'] ?? ''] ?? []);
        $subtypeLabel = $this->labelFor($selection['persona_subtype'] ?? '', $subtypes);

        $offerLabel = $this->labelFor($selection['offer_type'] ?? '', $catalog['offer_types'] ?? []);
        $maturityLabel = $this->labelFor($selection['maturity_level'] ?? '', $catalog['maturity_levels'] ?? []);
        $intentionLabel = $this->labelFor($selection['contact_intention'] ?? '', $catalog['intentions'] ?? []);

        $personaResolved = trim($personaLabel . ($subtypeLabel !== '' ? ' · ' . $subtypeLabel : ''));
        $customContext = trim((string) ($selection['custom_context'] ?? ''));

        $lines = [
            'Objectif principal: ' . ($objectiveLabel !== '' ? $objectiveLabel : 'Non précisé'),
            'Cible à analyser: ' . ($personaResolved !== '' ? $personaResolved : 'Non précisée'),
            'Offre proposée: ' . ($offerLabel !== '' ? $offerLabel : 'Non précisée'),
            'Niveau estimé du prospect: ' . ($maturityLabel !== '' ? $maturityLabel : 'Non précisé'),
            'Intention de contact: ' . ($intentionLabel !== '' ? $intentionLabel : 'Non précisée'),
        ];

        if ($customContext !== '') {
            $lines[] = 'Contexte additionnel: ' . $customContext;
        }

        $note = trim((string) ($selection['strategy_note'] ?? ''));
        if ($note !== '') {
            $lines[] = 'Orientation recommandée: ' . $note;
        }

        $lines[] = 'Produis une analyse orientée action, avec formulation adaptée à une prospection humaine et crédible.';

        return implode("\n", $lines);
    }

    /**
     * @param array<string,mixed> $selection
     * @param array<string,mixed> $catalog
     */
    public function buildHumanSummary(array $selection, array $catalog): string
    {
        $objective = $this->labelFor($selection['objective'] ?? '', $catalog['objectives'] ?? []);
        $persona = $this->labelFor($selection['persona_group'] ?? '', $catalog['persona_groups'] ?? []);
        $offer = $this->labelFor($selection['offer_type'] ?? '', $catalog['offer_types'] ?? []);

        $subtypes = (array) (($catalog['persona_subtypes'] ?? [])[$selection['persona_group'] ?? ''] ?? []);
        $subtype = $this->labelFor($selection['persona_subtype'] ?? '', $subtypes);
        $personaLabel = trim($persona . ($subtype !== '' ? ' (' . $subtype . ')' : ''));

        return sprintf(
            'Tu veux %s auprès de %s avec une proposition centrée sur %s.',
            $objective !== '' ? mb_strtolower($objective) : 'clarifier ta stratégie',
            $personaLabel !== '' ? mb_strtolower($personaLabel) : 'une cible à définir',
            $offer !== '' ? mb_strtolower($offer) : 'une offre à préciser'
        );
    }

    /** @param array<string,mixed> $options */
    private function pickKey(mixed $value, array $options): string
    {
        $key = trim((string) $value);
        return array_key_exists($key, $options) ? $key : '';
    }

    /** @param array<string,mixed> $options */
    private function labelFor(mixed $value, array $options): string
    {
        $key = trim((string) $value);
        if ($key === '' || !array_key_exists($key, $options)) {
            return '';
        }

        $item = $options[$key];
        if (is_array($item)) {
            return trim((string) ($item['label'] ?? ''));
        }

        return trim((string) $item);
    }
}
