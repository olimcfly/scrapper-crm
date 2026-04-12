<header class="topbar" aria-label="Topbar compacte">
  <div class="topbar-main">
    <p class="eyebrow">MVP mobile-first</p>
    <h1><?= htmlspecialchars((string) ($title ?? 'Dashboard')) ?></h1>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <div class="topbar-actions">
      <input class="search-input" type="search" placeholder="Rechercher un prospect" aria-label="Recherche rapide">
      <span class="user-chip"><?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?></span>
      <form method="post" action="/logout">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <button type="submit" class="btn secondary btn-compact">Déconnexion</button>
      </form>
    </div>
  <?php endif; ?>
</header>
