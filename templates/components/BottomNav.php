<?php

declare(strict_types=1);

$bottomNav = is_array($bottomNav ?? null) ? $bottomNav : [];
$currentPath = (string) ($currentPath ?? '');
?>

<?php if ($bottomNav !== []): ?>
  <nav class="bottom-nav" aria-label="Navigation mobile principale">
    <div class="bottom-nav-inner">
      <?php foreach ($bottomNav as $item): ?>
        <?php
          $path = (string) ($item['path'] ?? '#');
          $label = (string) ($item['label'] ?? 'Lien');
          $icon = (string) ($item['icon'] ?? '•');
          $isActive = $currentPath === $path;
        ?>
        <a
          class="bottom-nav-link <?= $isActive ? 'active' : '' ?>"
          href="<?= htmlspecialchars($path) ?>"
          aria-current="<?= $isActive ? 'page' : 'false' ?>"
        >
          <span class="bottom-nav-icon" aria-hidden="true">
            <?= htmlspecialchars($icon) ?>
          </span>
          <span class="bottom-nav-label">
            <?= htmlspecialchars($label) ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>
<?php endif; ?>