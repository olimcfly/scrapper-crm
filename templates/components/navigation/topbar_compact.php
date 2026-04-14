<header class="topbar">
  <div class="topbar-inner">
    <div class="topbar-left">
      <p class="topbar-title" role="heading" aria-level="1"><?= htmlspecialchars($title ?? 'CRM') ?></p>
      <?php if (!empty($pageDescription ?? '')): ?>
        <p class="topbar-subtitle"><?= htmlspecialchars((string) $pageDescription) ?></p>
      <?php endif; ?>
    </div>

    <div class="topbar-right">
      <?php if (!empty($authUser['first_name'] ?? '')): ?>
        <span class="topbar-user">
          Bonjour, <?= htmlspecialchars((string) $authUser['first_name']) ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</header>