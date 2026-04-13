<aside class="sidebar" aria-label="Navigation principale desktop">
  <div class="sidebar-brand">
    <div class="logo">SC</div>
    <div>
      <strong>CRM</strong>
      <div class="muted">Prospection intelligente</div>
    </div>
  </div>

  <p class="sidebar-section-title">Navigation</p>

  <nav class="sidebar-nav">
    <?php foreach (($adminModules ?? []) as $module): ?>
      <?php
        $modulePath = trim((string) ($module['path'] ?? ''));
        $moduleStatus = (string) ($module['status'] ?? 'active');
        $moduleLabel = (string) ($module['label'] ?? 'Module');
        $moduleIcon = (string) ($module['icon'] ?? '•');

        $isActive = $modulePath !== '' && (
          $currentPath === $modulePath ||
          str_starts_with($currentPath, rtrim($modulePath, '/') . '/')
        );
      ?>

      <a
        href="<?= htmlspecialchars($modulePath) ?>"
        class="sidebar-link<?= $isActive ? ' active' : '' ?>"
        <?= $isActive ? 'aria-current="page"' : '' ?>
      >
        <div class="sidebar-link-left">
          <span class="icon"><?= htmlspecialchars($moduleIcon) ?></span>
          <span><?= htmlspecialchars($moduleLabel) ?></span>
        </div>

        <?php if ($moduleStatus !== 'active'): ?>
          <span class="badge badge-<?= htmlspecialchars($moduleStatus) ?>">
            <?= htmlspecialchars((string) ($statusLabels[$moduleStatus] ?? $moduleStatus)) ?>
          </span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user">
      <div class="avatar">
        <?= htmlspecialchars(strtoupper(substr((string) ($authUser['first_name'] ?? 'U'), 0, 1))) ?>
      </div>
      <div>
        <strong><?= htmlspecialchars((string) ($authUser['first_name'] ?? 'Utilisateur')) ?></strong>
        <div class="muted">Session active</div>
      </div>
    </div>
  </div>
</aside>