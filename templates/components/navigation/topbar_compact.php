<header class="topbar">
  <div class="topbar-main">
    <h1><?= htmlspecialchars((string) ($title ?? 'CRM')) ?></h1>
    <p class="muted" style="margin-bottom:0;">Pilotage prospect-first · mobile-first</p>
  </div>
  <?php if ($isAuthenticated): ?>
    <div class="topbar-actions">
      <input class="search-input" type="search" placeholder="Recherche rapide" aria-label="Recherche rapide">
      <span style="display:inline-block;padding:6px 10px;background:var(--ds-color-surface-soft);border:1px solid #c7d2fe;border-radius:999px;color:#3730a3;font-size:13px;">
        <?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?>
      </span>
      <form method="post" action="/logout" style="display:inline;">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <button type="submit" class="btn secondary" style="min-height:36px;padding:6px 10px;">Déconnexion</button>
      </form>
    </div>
  <?php endif; ?>
</header>
