<?php
$desktopModules = is_array($desktopModules ?? null) ? $desktopModules : [];
$currentPath = (string) ($currentPath ?? '');
$statusLabels = is_array($statusLabels ?? null) ? $statusLabels : [];
$statusClassMap = is_array($statusClassMap ?? null) ? $statusClassMap : [];
?>

<aside class="sidebar" aria-label="Navigation principale">
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <strong>SCRAPPER CRM</strong>
      <small>Pilotage prospect</small>
    </div>
  </div>

  <div class="sidebar-body">
    <p class="sidebar-section-title">Navigation principale</p>

    <nav class="sidebar-nav" aria-label="Modules">
      <?php if ($desktopModules === []): ?>
        <div class="sidebar-empty">
          <p>Aucun module disponible.</p>
        </div>
      <?php else: ?>
        <?php foreach ($desktopModules as $module): ?>
          <?php
            $modulePath = (string) ($module['path'] ?? '#');
            $status = (string) ($module['status'] ?? 'placeholder');
            $isActive = $currentPath === $modulePath;
            $moduleLabel = (string) ($module['label'] ?? 'Module');
            $moduleDescription = (string) ($module['description'] ?? '');
            $statusClass = (string) ($statusClassMap[$status] ?? '');
            $statusLabel = (string) ($statusLabels[$status] ?? $status);
          ?>
          <a
            class="module-link <?= $isActive ? 'active' : '' ?>"
            href="<?= htmlspecialchars($modulePath) ?>"
            aria-current="<?= $isActive ? 'page' : 'false' ?>"
          >
            <span class="module-link-top">
              <span class="module-link-label"><?= htmlspecialchars($moduleLabel) ?></span>
              <span class="status-badge <?= htmlspecialchars($statusClass) ?>">
                <?= htmlspecialchars($statusLabel) ?>
              </span>
            </span>

            <?php if ($moduleDescription !== ''): ?>
              <small class="module-link-description">
                <?= htmlspecialchars($moduleDescription) ?>
              </small>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </nav>
  </div>
</aside>