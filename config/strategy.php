<?php

declare(strict_types=1);

return [
    'objectives' => [
        'clients' => ['label' => 'Trouver des clients', 'hint' => 'Générer des opportunités commerciales qualifiées.'],
        'partenaires' => ['label' => 'Trouver des partenaires', 'hint' => 'Identifier des alliances utiles et complémentaires.'],
        'recrutement' => ['label' => 'Recruter', 'hint' => 'Attirer des profils adaptés à votre activité.'],
        'concurrence' => ['label' => 'Observer la concurrence', 'hint' => 'Analyser le marché pour mieux se différencier.'],
        'contenu' => ['label' => 'Générer du contenu', 'hint' => 'Trouver des angles éditoriaux qui résonnent.'],
    ],
    'persona_groups' => [
        'independant' => ['label' => 'Indépendant / freelance', 'hint' => 'Activité solo, besoin de régularité commerciale.'],
        'dirigeant_local' => ['label' => 'Dirigeant entreprise locale', 'hint' => 'PME/TPE avec enjeux de visibilité locale.'],
        'bien_etre' => ['label' => 'Praticien bien-être', 'hint' => 'Activité relationnelle, forte importance de confiance.'],
        'immobilier' => ['label' => 'Agent immobilier', 'hint' => 'Cycle de décision et suivi multi-étapes.'],
        'coach_formateur' => ['label' => 'Coach / formateur', 'hint' => 'Besoin de crédibilité et de preuve de résultat.'],
        'autre' => ['label' => 'Autre', 'hint' => 'Cas spécifique à détailler dans le résumé.'],
    ],
    'persona_subtypes' => [
        'bien_etre' => [
            'masseuse' => 'Masseuse',
            'sophrologue' => 'Sophrologue',
            'naturopathe' => 'Naturopathe',
            'energeticienne' => 'Énergéticienne',
            'reflexologue' => 'Réflexologue',
            'autre' => 'Autre',
        ],
    ],
    'offer_types' => [
        'service' => 'Service',
        'coaching' => 'Coaching',
        'formation' => 'Formation',
        'produit' => 'Produit',
        'accompagnement_local' => 'Accompagnement local',
        'acquisition_client' => 'Acquisition client',
        'visibilite_digitale' => 'Visibilité digitale',
        'automatisation' => 'Automatisation',
    ],
    'maturity_levels' => [
        'ignore_probleme' => 'Le prospect ne voit pas encore son problème',
        'ressent_sans_savoir' => 'Le prospect sent qu’il galère mais ne sait pas pourquoi',
        'cherche_solution' => 'Le prospect cherche déjà une solution',
        'compare_options' => 'Le prospect compare les options',
        'pret_action' => 'Le prospect est potentiellement prêt à passer à l’action',
    ],
    'intentions' => [
        'vendre_maintenant' => 'Vendre maintenant',
        'creer_lien' => 'Créer un lien',
        'apporter_valeur' => 'Apporter de la valeur',
        'qualifier_contact' => 'Qualifier avant prise de contact',
    ],
    'defaults_by_persona' => [
        'bien_etre' => [
            'objective' => 'clients',
            'offer_type' => 'visibilite_digitale',
            'maturity_level' => 'ressent_sans_savoir',
            'intention' => 'creer_lien',
            'strategy_note' => 'Approche recommandée : pédagogie simple + preuve locale + différenciation de pratique.',
        ],
    ],
];
