<?php
$ctaLabel = (string) ($ctaLabel ?? 'Action principale');
$ctaHref = (string) ($ctaHref ?? '#');
$ctaClass = (string) ($ctaClass ?? '');
?>

<a
  class="btn btn-primary <?= htmlspecialchars($ctaClass) ?>"
  href="<?= htmlspecialchars($ctaHref) ?>"
>
  <span class="btn-label"><?= htmlspecialchars($ctaLabel) ?></span>
</a>