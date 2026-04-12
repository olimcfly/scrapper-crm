<header class="topbar">
  <div class="topbar-main">
    <h1><?= htmlspecialchars((string) ($title ?? 'CRM')) ?></h1>
    <p class="topbar-subtitle">Pilotage prospect-first · mobile-first</p>
  </div>
  <?php if ($isAuthenticated): ?>
    <div class="topbar-actions">
      <input class="search-input" type="search" placeholder="Recherche rapide" aria-label="Recherche rapide">
      <span class="user-badge"><?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?></span>
      <form method="post" action="/logout" class="logout-form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <button type="submit" class="btn secondary compact">Déconnexion</button>
      </form>
    </div>
  <?php endif; ?>
</header>
