<?php
$emptyTitle = (string) ($emptyTitle ?? 'Aucune donnée pour le moment');
$emptyDescription = (string) ($emptyDescription ?? 'Ajoutez un prospect pour débloquer le flux de travail prospect-first.');
$emptyCtaLabel = (string) ($emptyCtaLabel ?? 'Ajouter un prospect');
$emptyCtaHref = (string) ($emptyCtaHref ?? '/prospects/create');
?>
<section class="card empty-guided" aria-live="polite">
  <p class="eyebrow">Empty state</p>
  <h3><?= htmlspecialchars($emptyTitle) ?></h3>
  <p class="muted"><?= htmlspecialchars($emptyDescription) ?></p>
  <?php
    $label = $emptyCtaLabel;
    $href = $emptyCtaHref;
    $icon = '➕';
    include __DIR__ . '/PrimaryCTAButton.php';
  ?>
</section>
