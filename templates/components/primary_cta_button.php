<?php

declare(strict_types=1);

$href = isset($href) ? (string) $href : null;
$label = (string) ($label ?? 'Action');
$variant = (string) ($variant ?? 'primary');

$baseClass = 'btn primary-cta-button';
$variantClass = $variant === 'secondary' ? 'btn-secondary' : 'btn-primary';

$className = trim($baseClass . ' ' . $variantClass . ' ' . (string) ($className ?? ''));
?>

<?php if ($href !== null): ?>
  <a class="<?= htmlspecialchars($className) ?>" href="<?= htmlspecialchars($href) ?>">
    <span class="btn-label"><?= htmlspecialchars($label) ?></span>
  </a>
<?php else: ?>
  <button 
    class="<?= htmlspecialchars($className) ?>" 
    type="<?= htmlspecialchars((string) ($type ?? 'button')) ?>"
  >
    <span class="btn-label"><?= htmlspecialchars($label) ?></span>
  </button>
<?php endif; ?>