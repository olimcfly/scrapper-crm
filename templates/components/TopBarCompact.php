<header class="topbar" aria-label="Topbar compacte">
  
  <div class="topbar-main">

    <h1 class="topbar-title">
      <?= htmlspecialchars((string) ($title ?? 'Dashboard')) ?>
    </h1>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <div class="topbar-actions">
      
      <input 
        class="topbar-search" 
        type="search" 
        placeholder="Rechercher un prospect" 
        aria-label="Recherche rapide"
      >

      <span class="user-chip">
        <?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?>
      </span>

      <form method="post" action="/logout" class="logout-form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        
        <button type="submit" class="btn btn-secondary btn-compact">
          Déconnexion
        </button>
      </form>

    </div>
  <?php endif; ?>

</header>