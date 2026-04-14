<div class="auth-shell auth-shell-verify">
  <div class="auth-background-glow auth-background-glow-1"></div>
  <div class="auth-background-glow auth-background-glow-2"></div>

  <div class="auth-layout">
    <section class="auth-visual" aria-label="Présentation SCRAPPER CRM">
      <div class="auth-visual-inner">
        <span class="auth-badge">Vérification sécurisée</span>

        <div class="auth-hero-copy">
          <h1 class="auth-brand">SCRAPPER CRM</h1>
          <p class="auth-tagline">
            Une étape rapide pour sécuriser l’accès à votre espace et retrouver vos prospects.
          </p>
        </div>

        <div class="auth-kpi-panel">
          <div class="auth-kpi">
            <strong>1</strong>
            <span>Code unique</span>
          </div>
          <div class="auth-kpi">
            <strong>6</strong>
            <span>Chiffres</span>
          </div>
          <div class="auth-kpi">
            <strong>100%</strong>
            <span>Accès sécurisé</span>
          </div>
        </div>
      </div>
    </section>

    <section class="auth-panel">
      <div class="auth-card">
        <div class="auth-card-top">
          <p class="auth-overline">Validation</p>
          <h2 class="auth-title">Code de vérification</h2>
          <p class="auth-subtitle">
            Un code à 6 chiffres a été envoyé à votre adresse email.
          </p>
        </div>

        <div class="auth-email-box">
          Code envoyé à <strong><?= htmlspecialchars($email ?? '') ?></strong>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="auth-alert auth-alert-danger">
            <strong>Erreur</strong>
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="/login/verify" novalidate class="auth-form">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

          <div class="auth-field">
            <label class="auth-label" for="code">Code à 6 chiffres</label>
            <input
              class="auth-input auth-code-input"
              id="code"
              type="text"
              name="code"
              inputmode="numeric"
              pattern="\d{6}"
              maxlength="6"
              autocomplete="one-time-code"
              autofocus
              placeholder="000000"
              required
            >
          </div>

          <button type="submit" class="auth-button">
            Valider et accéder
          </button>
        </form>

        <a class="auth-link" href="/login">← Demander un nouveau code</a>

        <div class="auth-reassurance">
          <span class="auth-reassurance-dot"></span>
          <p>Connexion sécurisée par code temporaire</p>
        </div>
      </div>
    </section>
  </div>
</div>