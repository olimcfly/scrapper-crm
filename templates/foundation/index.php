<?php
function fval(array $f, string $k): string { return htmlspecialchars((string) ($f[$k] ?? '')); }
$f = is_array($foundation ?? null) ? $foundation : [];
$c = is_array($completion ?? null) ? $completion : ['percent' => 0, 'filled_total' => 0, 'required_total' => 0];
?>
<div class="foundation-page">
  <header class="card foundation-hero">
    <div>
      <p class="foundation-kicker">Cerveau métier client</p>
      <h1>Fondation stratégique</h1>
      <p class="subtitle">Renseigne une seule fois ton socle business puis réutilise-le partout dans le CRM.</p>
    </div>
    <div class="foundation-progress">
      <strong><?= (int) $c['percent'] ?>%</strong>
      <span><?= (int) $c['filled_total'] ?>/<?= (int) $c['required_total'] ?> champs clés complétés</span>
      <div class="studio-progress-track"><span style="width: <?= (int) $c['percent'] ?>%"></span></div>
    </div>
  </header>

  <?php if (!empty($successMessage)): ?><div class="global-state success"><span class="state-dot"></span><p><?= htmlspecialchars((string) $successMessage) ?></p></div><?php endif; ?>
  <?php if (!empty($warningMessage)): ?><div class="global-state warning"><span class="state-dot"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div><?php endif; ?>

  <div class="foundation-quick-links card">
    <a class="btn btn-secondary" href="/contenu">Utiliser dans le Studio</a>
    <a class="btn btn-secondary" href="/messages-ia">Utiliser dans Messages IA</a>
    <a class="btn btn-secondary" href="/strategie">Nourrir l’analyse</a>
    <a class="btn btn-primary" href="/pages-publiques">Créer une page publique</a>
  </div>

  <form method="post" action="/fondation-strategique" class="foundation-form">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

    <?php
    $sections = [
      'Identité professionnelle' => [
        'business_name' => 'Nom de l’activité', 'first_name' => 'Prénom', 'last_name' => 'Nom', 'role_title' => 'Métier / fonction',
        'primary_city' => 'Ville principale', 'service_area' => 'Zone d’intervention', 'target_client_type' => 'Type de client ciblé',
        'primary_contacts' => 'Coordonnées principales', 'website_url' => 'Site web', 'social_links' => 'Réseaux sociaux',
      ],
      'Positionnement' => [
        'who_i_help' => 'Qui j’aide', 'main_problem_solved' => 'Problème principal résolu', 'target_persona' => 'Type de personne/client',
        'differentiator' => 'Ce qui me différencie', 'why_choose_me' => 'Pourquoi on me choisit', 'what_i_do_not_do' => 'Ce que je ne fais pas', 'communication_tone' => 'Style / ton',
      ],
      'Promesse' => [
        'core_promise' => 'Promesse principale', 'promised_transformation' => 'Transformation promise', 'core_benefits' => 'Bénéfices principaux',
        'expected_result' => 'Résultat concret attendu', 'promise_timeline' => 'Délai/horizon', 'short_promise_phrase' => 'Phrase courte', 'long_promise_version' => 'Version développée',
      ],
      'Offre détaillée' => [
        'offer_name' => 'Nom de l’offre', 'offer_subtitle' => 'Sous-titre', 'offer_for_who' => 'Pour qui', 'offer_problem' => 'Problème traité', 'offer_content' => 'Contenu',
        'offer_steps' => 'Étapes / déroulé', 'offer_deliverables' => 'Livrables', 'offer_bonus' => 'Bonus', 'offer_guarantee' => 'Garantie', 'offer_price' => 'Prix', 'offer_terms' => 'Modalités',
        'offer_common_objections' => 'Objections fréquentes', 'offer_objection_answers' => 'Réponses', 'offer_primary_cta' => 'CTA principal',
      ],
      'Preuves / crédibilité' => [
        'testimonials' => 'Témoignages', 'results_obtained' => 'Résultats obtenus', 'experience_text' => 'Expérience', 'certifications' => 'Certifications',
        'method_process' => 'Méthode / process', 'values_text' => 'Valeurs', 'reassurance_elements' => 'Éléments de réassurance',
      ],
      'Paramètres de production' => [
        'send_email' => 'Email d’envoi principal', 'email_signature' => 'Signature email', 'sender_display_name' => 'Nom affiché expéditeur',
        'primary_domain' => 'Nom de domaine principal', 'desired_public_url' => 'URL publique souhaitée', 'production_main_cta' => 'CTA principal',
        'booking_link' => 'Lien prise de rendez-vous', 'whatsapp_link' => 'Lien WhatsApp', 'download_link' => 'Lien téléchargement', 'internal_strategy_notes' => 'Notes internes',
      ],
    ];
    $open = true;
    foreach ($sections as $sectionTitle => $fields): ?>
      <details class="card foundation-section" <?= $open ? 'open' : '' ?>>
        <summary><strong><?= htmlspecialchars($sectionTitle) ?></strong></summary>
        <div class="settings-grid two-cols">
          <?php foreach ($fields as $key => $label): $isLong = in_array($key, ['primary_contacts','social_links','who_i_help','main_problem_solved','target_persona','differentiator','why_choose_me','what_i_do_not_do','communication_tone','core_promise','promised_transformation','core_benefits','expected_result','long_promise_version','offer_for_who','offer_problem','offer_content','offer_steps','offer_deliverables','offer_bonus','offer_guarantee','offer_terms','offer_common_objections','offer_objection_answers','offer_primary_cta','testimonials','results_obtained','experience_text','certifications','method_process','values_text','reassurance_elements','email_signature','production_main_cta','internal_strategy_notes'], true); ?>
            <label class="form-field <?= $isLong ? 'settings-grid-full' : '' ?>"><span><?= htmlspecialchars($label) ?></span>
              <?php if ($isLong): ?>
                <textarea name="<?= htmlspecialchars($key) ?>" rows="3" placeholder="Complète ce champ pour mieux guider l’IA..."><?= fval($f, $key) ?></textarea>
              <?php else: ?>
                <input type="text" name="<?= htmlspecialchars($key) ?>" value="<?= fval($f, $key) ?>" placeholder="<?= htmlspecialchars($label) ?>">
              <?php endif; ?>
            </label>
          <?php endforeach; ?>
        </div>
      </details>
    <?php $open = false; endforeach; ?>

    <div class="foundation-actions"><button class="btn btn-primary" type="submit">Enregistrer ma fondation</button></div>
  </form>
</div>
