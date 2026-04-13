<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/auth.css">
<div class="login-page">

  <section class="login-visual" aria-label="Mon CRM">
    <div class="login-visual-content">

      <span class="login-eyebrow">Prospection organique assistée par IA</span>

      <h1 class="login-title-main">MON CRM</h1>

      <p class="login-description">
        Centralisez vos prospects et pilotez vos actions avec clarté.
      </p>

      <div class="login-features">
        <div class="login-feature">
          <span class="feature-dot"></span>
          <span>Vue claire des opportunités</span>
        </div>

        <div class="login-feature">
          <span class="feature-dot"></span>
          <span>Connexion sécurisée par code</span>
        </div>
      </div>

    </div>
  </section>

  <section class="login-form-area">
    <div class="login-form-shell">

      <div class="card login-card">

        <p class="form-eyebrow">Vérification</p>

        <h2 class="form-title">Entrez votre code</h2>

        <p class="form-description">
          Code envoyé à <strong><?= htmlspecialchars((string) ($email ?? '')) ?></strong>
        </p>

        <?php if (!empty($errors)): ?>
          <div class="global-state global-error">
            <span class="state-dot"></span>
            <div class="state-content">
              <strong class="state-title">Erreur</strong>
              <div class="state-message">Code invalide</div>
            </div>
          </div>
        <?php endif; ?>

        <form method="post" action="/login/verify" class="login-form" id="verifyForm">

          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
          <input type="hidden" name="code" id="hiddenCode">

          <div class="code-group">
            <?php for ($i = 0; $i < 6; $i++): ?>
              <input
                type="text"
                maxlength="1"
                inputmode="numeric"
                class="code-input"
              >
            <?php endfor; ?>
          </div>

          <button type="submit" class="btn btn-primary btn-cta" id="verifyBtn" disabled>
            Vérifier
          </button>

          <p class="form-footnote">
            Code valable 10 minutes
          </p>

        </form>

      </div>

    </div>
  </section>

</div>