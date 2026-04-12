<?php

declare(strict_types=1);

$title = (string) ($title ?? 'Aucune donnée pour le moment');
$message = (string) ($message ?? 'Créez votre premier prospect pour lancer le flux prospect-first.');
$ctaHref = isset($ctaHref) ? (string) $ctaHref : '/prospects/create';
$ctaLabel = (string) ($ctaLabel ?? 'Ajouter un prospect');
?>
<section class="card empty-state-guided" aria-label="État vide">
  <p class="empty-eyebrow">Prêt à démarrer</p>
  <h3><?= htmlspecialchars($title) ?></h3>
  <p class="muted"><?= htmlspecialchars($message) ?></p>
  <?php
    $href = $ctaHref;
    $label = $ctaLabel;
    $variant = 'primary';
    require __DIR__ . '/primary_cta_button.php';
  ?>
</section>
