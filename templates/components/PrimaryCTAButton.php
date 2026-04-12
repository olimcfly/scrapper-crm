<?php
$ctaLabel = (string) ($label ?? 'Action');
$ctaHref = isset($href) ? (string) $href : null;
$ctaType = (string) ($type ?? 'button');
$ctaIcon = (string) ($icon ?? '');
$ctaVariant = (string) ($variant ?? 'primary');
$ctaClasses = 'btn btn-cta';

if ($ctaVariant !== 'primary') {
    $ctaClasses .= ' btn-' . preg_replace('/[^a-z0-9\-]/i', '', $ctaVariant);
}
?>
<?php if ($ctaHref !== null): ?>
  <a class="<?= htmlspecialchars($ctaClasses) ?>" href="<?= htmlspecialchars($ctaHref) ?>">
    <?php if ($ctaIcon !== ''): ?><span aria-hidden="true"><?= htmlspecialchars($ctaIcon) ?></span><?php endif; ?>
    <span><?= htmlspecialchars($ctaLabel) ?></span>
  </a>
<?php else: ?>
  <button class="<?= htmlspecialchars($ctaClasses) ?>" type="<?= htmlspecialchars($ctaType) ?>">
    <?php if ($ctaIcon !== ''): ?><span aria-hidden="true"><?= htmlspecialchars($ctaIcon) ?></span><?php endif; ?>
    <span><?= htmlspecialchars($ctaLabel) ?></span>
  </button>
<?php endif; ?>
