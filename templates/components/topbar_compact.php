<header class="topbar topbar-compact">
  
  <div class="topbar-main">
    <h1 class="topbar-title">
      <?= htmlspecialchars((string) ($title ?? 'CRM')) ?>
    </h1>

   
  </div>

  <?php if (($isAuthenticated ?? false) === true): ?>
    <div class="topbar-actions">
      
      <input 
        class="topbar-search" 
        type="search" 
        placeholder="Recherche rapide" 
        aria-label="Recherche rapide"
      >

      <span class="user-chip">
        <?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?>
      </span>

      <form method="post" action="/logout" class="logout-form">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        
        <button type="submit" class="btn btn-secondary btn-compact">
          D¨¦connexion
        </button>
      </form>

    </div>
  <?php endif; ?>

</header>