<?php
$desktopModules = is_array($desktopModules ?? null) ? $desktopModules : [];
$currentPath = (string) ($currentPath ?? '');
$statusLabels = is_array($statusLabels ?? null) ? $statusLabels : [];
$statusClassMap = is_array($statusClassMap ?? null) ? $statusClassMap : [];
?>
<aside class="sidebar" aria-label="Navigation desktop">
  <div class="brand">SCRAPPER CRM</div>
  <p class="sidebar-section-title">Navigation principale</p>

  <?php foreach ($desktopModules as $module): ?>
    <?php
      $modulePath = (string) ($module['path'] ?? '#');
      $status = (string) ($module['status'] ?? 'placeholder');
      $isActive = $currentPath === $modulePath;
    ?>
    <a class="module-link <?= $isActive ? 'active' : '' ?>" href="<?= htmlspecialchars($modulePath) ?>">
      <span class="label-row">
        <span><?= htmlspecialchars((string) ($module['label'] ?? 'Module')) ?></span>
        <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$status] ?? '')) ?>">
          <?= htmlspecialchars((string) ($statusLabels[$status] ?? $status)) ?>
        </span>
      </span>
      <small><?= htmlspecialchars((string) ($module['description'] ?? '')) ?></small>
    </a>
  <?php endforeach; ?>
</aside>
