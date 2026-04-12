<?php
$bottomNavItems = is_array($bottomNavItems ?? null) ? $bottomNavItems : [];
$currentPath = (string) ($currentPath ?? '');
?>
<nav class="bottom-nav" aria-label="Navigation mobile principale">
  <?php foreach ($bottomNavItems as $item): ?>
    <?php
      $itemPath = (string) ($item['path'] ?? '#');
      $isActive = $currentPath === $itemPath;
    ?>
    <a class="bottom-nav-link <?= $isActive ? 'active' : '' ?>" href="<?= htmlspecialchars($itemPath) ?>">
      <span class="nav-icon" aria-hidden="true"><?= htmlspecialchars((string) ($item['icon'] ?? '•')) ?></span>
      <span><?= htmlspecialchars((string) ($item['label'] ?? 'Module')) ?></span>
    </a>
  <?php endforeach; ?>
</nav>
