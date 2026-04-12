<?php
$ctaLabel = (string) ($ctaLabel ?? 'Action principale');
$ctaHref = (string) ($ctaHref ?? '#');
$ctaClass = (string) ($ctaClass ?? '');
?>
<a class="btn primary-cta-button <?= htmlspecialchars($ctaClass) ?>" href="<?= htmlspecialchars($ctaHref) ?>">
  <?= htmlspecialchars($ctaLabel) ?>
</a>
