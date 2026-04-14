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
    <div class="prospect-identity">
      <h3 class="prospect-name"><?= htmlspecialchars($fullName) ?></h3>
      <p class="prospect-meta"><?= htmlspecialchars($activity) ?> · <?= htmlspecialchars($city) ?></p>
    </div>
    <span class="status-pill" style="background:#eef2ff;color:#3730a3;border-color:#c7d2fe;"><?= htmlspecialchars($status) ?></span>
  </div>

  <div class="prospect-badges">
    <?php $badgeLabel = 'Score ' . $score; $badgeClass = 'score-pill'; require __DIR__ . '/prospect_badge.php'; ?>
    <?php $badgeLabel = $awareness; $badgeClass = 'awareness-pill ' . $awarenessClass; require __DIR__ . '/prospect_badge.php'; ?>
    <?php $badgeLabel = $priorityLabel; $badgeClass = 'priority-pill ' . $priorityClass; require __DIR__ . '/prospect_badge.php'; ?>
  </div>

  <div class="presence-grid">
    <?php $indicatorLabel = 'Site web'; $indicatorValue = $hasWebsite ? 'Oui' : 'Non'; $indicatorState = $hasWebsite ? 'ok' : 'off'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Réseaux'; $indicatorValue = $hasSocial ? 'Oui' : 'Non'; $indicatorState = $hasSocial ? 'ok' : 'off'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Catégorie'; $indicatorValue = $category; $indicatorState = 'neutral'; require __DIR__ . '/prospect_indicator.php'; ?>
    <?php $indicatorLabel = 'Zone'; $indicatorValue = $zoneScope; $indicatorState = 'neutral'; require __DIR__ . '/prospect_indicator.php'; ?>
  </div>

  <div class="quick-actions quick-actions-primary">
    <a class="quick-action primary" data-quick-action="view" href="/prospects/<?= (int) $prospectCard['id'] ?>">Voir</a>
    <a class="quick-action ia primary" data-quick-action="ai-analysis" href="/strategie">Analyse IA</a>
  </div>

  <div class="quick-actions quick-actions-secondary">
    <a class="quick-action secondary" data-quick-action="generate-message" href="/prospects/<?= (int) $prospectCard['id'] ?>/generated-contents">Générer message</a>
    <a class="quick-action secondary" data-quick-action="add-pipeline" href="/pipeline#prospect-<?= (int) $prospectCard['id'] ?>">Ajouter au pipeline</a>
  </div>
</article>
