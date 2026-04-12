<?php

declare(strict_types=1);
?>
<header class="topbar topbar-compact">
  <div class="topbar-main">
    <h1><?= htmlspecialchars((string) ($title ?? 'CRM')) ?></h1>
    <p class="muted" style="margin-bottom:0;">Pilotage prospect-first · mobile-first</p>
  </div>
  <?php if (($isAuthenticated ?? false) === true): ?>
    <div class="topbar-actions">
      <input class="search-input" type="search" placeholder="Recherche rapide" aria-label="Recherche rapide">
      <span class="user-chip"><?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?></span>
      <form method="post" action="/logout" style="display:inline;">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <button type="submit" class="btn secondary compact">Déconnexion</button>
      </form>
    </div>
  <?php endif; ?>
</header>
