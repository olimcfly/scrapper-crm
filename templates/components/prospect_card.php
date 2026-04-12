<?php
$prospectCard = is_array($prospectCard ?? null) ? $prospectCard : [];
$detectCategory = $detectCategory ?? null;

if ($prospectCard === []) {
    return;
}

$fullName = trim((string) ($prospectCard['full_name'] ?? (($prospectCard['first_name'] ?? '') . ' ' . ($prospectCard['last_name'] ?? ''))));
$fullName = $fullName !== '' ? $fullName : 'Prospect sans nom';
$activity = (string) ($prospectCard['activity'] ?? 'Activité non renseignée');
$city = (string) ($prospectCard['city'] ?? 'Ville inconnue');

$score = (int) ($prospectCard['score'] ?? 0);
$priorityRaw = (string) ($prospectCard['niveau_priorite'] ?? 'moyen');
$status = trim((string) ($prospectCard['status_name'] ?? 'À qualifier')) ?: 'À qualifier';

$awareness = 'À éduquer';
if ($score >= 75) {
    $awareness = 'Prêt à décider';
} elseif ($score >= 45) {
    $awareness = 'Conscient du besoin';
}

$awarenessClass = match ($awareness) {
    'Prêt à décider' => 'awareness-hot',
    'Conscient du besoin' => 'awareness-warm',
    default => 'awareness-cold',
};

$priorityClass = match ($priorityRaw) {
    'eleve' => 'priority-high',
    'faible' => 'priority-low',
    default => 'priority-medium',
};

$priorityLabel = match ($priorityRaw) {
    'eleve' => 'Haute priorité',
    'faible' => 'Priorité basse',
    default => 'Priorité moyenne',
};

$category = is_callable($detectCategory) ? (string) $detectCategory($prospectCard) : 'Autres';
$hasWebsite = trim((string) ($prospectCard['website'] ?? '')) !== '';
$hasSocial = trim((string) ($prospectCard['instagram_url'] ?? '')) !== ''
    || trim((string) ($prospectCard['facebook_url'] ?? '')) !== ''
    || trim((string) ($prospectCard['linkedin_url'] ?? '')) !== ''
    || trim((string) ($prospectCard['tiktok_url'] ?? '')) !== '';

$zoneScope = (trim((string) ($prospectCard['country'] ?? '')) === '' || mb_strtolower((string) ($prospectCard['country'] ?? '')) === 'france')
    ? 'Locale'
    : 'Multi-zone';

$priorityBoost = match ($priorityRaw) {
    'eleve' => 12,
    'faible' => -8,
    default => 0,
};
$opportunityScore = max(0, min(100, $score + $priorityBoost));

$approachAngle = match ($awareness) {
    'Prêt à décider' => 'Proposer une offre claire avec preuve sociale locale.',
    'Conscient du besoin' => 'Éduquer sur le ROI avec un cas concret proche de son activité.',
    default => 'Créer de la confiance via contenu pédagogique et valeur rapide.',
};

$contentStrategy = match ($category) {
    'Restaurants' => 'Story courte “avant/après” + post coulisses du service.',
    'Agents immobiliers' => 'Mini analyse de quartier + preuve terrain en carrousel.',
    'Thérapeutes' => 'Post éducatif + témoignage anonymisé pour rassurer.',
    'Commerçants' => 'UGC client + offre locale en série de 3 posts.',
    default => 'Contenu preuve + pédagogie + appel à action local.',
};

$firstMessage = match ($awareness) {
    'Prêt à décider' => 'Bonjour ' . $fullName . ', je vous propose un plan court pour générer plus de demandes qualifiées dès ce mois-ci.',
    'Conscient du besoin' => 'Bonjour ' . $fullName . ', j’ai identifié 2 leviers simples pour améliorer votre acquisition organique localement.',
    default => 'Bonjour ' . $fullName . ', je partage une idée de contenu simple pour attirer vos prochains prospects sans pub.',
};

$cardState = (string) ($cardState ?? 'default');
?>
<article
  class="prospect-card"
  data-card
  data-state="<?= htmlspecialchars($cardState) ?>"
  data-category="<?= htmlspecialchars($category) ?>"
  data-search="<?= htmlspecialchars($fullName . ' ' . $activity . ' ' . $city) ?>"
  data-city="<?= htmlspecialchars($city) ?>"
  data-awareness="<?= htmlspecialchars($awareness) ?>"
  data-social="<?= $hasSocial ? 'oui' : 'non' ?>"
  data-website="<?= $hasWebsite ? 'oui' : 'non' ?>"
  data-priority="<?= htmlspecialchars($priorityRaw) ?>"
  data-status="<?= (int) ($prospectCard['status_id'] ?? 0) ?>"
  data-zone="<?= htmlspecialchars($zoneScope) ?>"
>
  <div class="prospect-top">
    <div>
      <h3 class="prospect-name"><?= htmlspecialchars($fullName) ?></h3>
      <p class="prospect-meta"><?= htmlspecialchars($activity) ?> · <?= htmlspecialchars($city) ?></p>
    </div>
    <span class="status-pill crm-status-pill"><?= htmlspecialchars($status) ?></span>
  </div>

  <div class="prospect-badges">
    <?php $badgeLabel = 'Score ' . $score; $badgeClass = 'score-pill'; require __DIR__ . '/prospect_badge.php'; ?>
    <?php $badgeLabel = 'Opportunité ' . $opportunityScore . '/100'; $badgeClass = 'ia-opportunity-pill'; require __DIR__ . '/prospect_badge.php'; ?>
    <?php $badgeLabel = $awareness; $badgeClass = 'awareness-pill ' . $awarenessClass; require __DIR__ . '/prospect_badge.php'; ?>
    <?php $badgeLabel = $priorityLabel; $badgeClass = 'priority-pill ' . $priorityClass; require __DIR__ . '/prospect_badge.php'; ?>
  </div>

  <div class="presence-grid">
    <?php $indicatorLabel = 'Site web'; $indicatorValue = $hasWebsite ? 'Oui' : 'Non'; $indicatorState = $hasWebsite ? 'ok' : 'off'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Réseaux'; $indicatorValue = $hasSocial ? 'Oui' : 'Non'; $indicatorState = $hasSocial ? 'ok' : 'off'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Catégorie'; $indicatorValue = $category; $indicatorState = 'neutral'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Zone'; $indicatorValue = $zoneScope; $indicatorState = 'neutral'; require __DIR__ . '/prospect_indicator.php'; ?>
  </div>

  <section class="ai-assist-block">
    <p class="ai-assist-kicker">Assistant IA</p>
    <p class="ai-assist-line"><strong>Angle d’approche :</strong> <?= htmlspecialchars($approachAngle) ?></p>
    <p class="ai-assist-line"><strong>Stratégie contenu :</strong> <?= htmlspecialchars($contentStrategy) ?></p>
    <p class="ai-assist-preview"><?= htmlspecialchars($firstMessage) ?></p>
  </section>

  <div class="quick-actions">
    <a class="quick-action" data-quick-action="view" href="/prospects/<?= (int) $prospectCard['id'] ?>">Voir</a>
    <a class="quick-action ia" data-quick-action="ai-analysis" href="/prospects/<?= (int) $prospectCard['id'] ?>">Analyser profil</a>
    <a class="quick-action" data-quick-action="generate-message" href="/prospects/<?= (int) $prospectCard['id'] ?>/generated-contents">Générer message IA</a>
    <a class="quick-action" data-quick-action="content-strategy" href="/prospects/<?= (int) $prospectCard['id'] ?>/generated-contents?type=post">Stratégie contenu</a>
    <a class="quick-action" data-quick-action="add-pipeline" href="/pipeline#prospect-<?= (int) $prospectCard['id'] ?>">Ajouter au pipeline</a>
  </div>
</article>
