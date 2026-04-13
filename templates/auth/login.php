<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/auth.css">

<div class="login-page">

  <section class="login-visual" aria-label="CRM">
    <div class="login-visual-content">

      <div class="login-visual-top">
        <span class="login-eyebrow">Prospection organique assistée par IA</span>

        <h1 class="login-title-main">MON CRM</h1>

        <p class="login-description">
          Centralisez vos prospects, pilotez vos relances et transformez votre activité
          avec une interface pensée pour l'action.
        </p>

        <div class="login-features">
          <div class="login-feature">
            <span class="feature-dot"></span>
            <span>Vue claire sur vos prospects et opportunités</span>
          </div>

          <div class="login-feature">
            <span class="feature-dot"></span>
            <span>Connexion rapide par code sécurisé</span>
          </div>

          <div class="login-feature">
            <span class="feature-dot"></span>
            <span>Base évolutive vers un CRM intelligent</span>
          </div>
        </div>
      </div>

      <div class="login-visual-bottom">
        <div class="kpi-panel">
          <p class="kpi-title">Activité</p>

          <div class="kpi-grid">
            <div class="kpi"><strong>24</strong><span>Prospects</span></div>
            <div class="kpi"><strong>11</strong><span>Pipeline</span></div>
            <div class="kpi"><strong>7</strong><span>Actions</span></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <section class="login-form-area">
    <div class="login-form-shell">

      <div class="login-mobile-brand">
        <span class="brand-mark">SC</span>
        <span>SCRAPPER CRM</span>
      </div>

      <div class="card login-card">

        <p class="form-eyebrow">Accès sécurisé</p>

        <h2 class="form-title">Connexion</h2>

        <p class="form-description">
          Entrez votre email pour recevoir un code temporaire sécurisé.
        </p>

        <?php if (!empty($errors)): ?>
          <div class="global-state global-error">
            <span class="state-dot"></span>
            <div class="state-content">
              <strong class="state-title">Erreur</strong>
              <div class="state-message">
                Impossible de finaliser la connexion
              </div>
            </div>
          </div>
        <?php endif; ?>

        <form method="post" action="/login" class="login-form">

          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

          <div class="form-field">
            <label class="form-label" for="email">Email</label>

            <input 
              class="form-input" 
              id="email" 
              type="email" 
              name="email"
              placeholder="vous@entreprise.fr"
              required
              value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>"
            >
          </div>

          <button type="submit" class="btn btn-primary btn-cta">
            Recevoir mon code
          </button>

          <p class="form-footnote">
            Connexion sécurisée · sans mot de passe
          </p>

        </form>

      </div>

    </div>
  </section>

</div>