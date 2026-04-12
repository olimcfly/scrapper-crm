<?php

declare(strict_types=1);

$href = isset($href) ? (string) $href : null;
$label = (string) ($label ?? 'Action');
$variant = (string) ($variant ?? 'primary');
$className = trim('btn primary-cta-button ' . ($variant === 'secondary' ? 'secondary' : '') . ' ' . (string) ($className ?? ''));
?>
<?php if ($href !== null): ?>
  <a class="<?= htmlspecialchars($className) ?>" href="<?= htmlspecialchars($href) ?>">
    <?= htmlspecialchars($label) ?>
  </a>
<?php else: ?>
  <button class="<?= htmlspecialchars($className) ?>" type="<?= htmlspecialchars((string) ($type ?? 'button')) ?>">
    <?= htmlspecialchars($label) ?>
  </button>
<?php endif; ?>
