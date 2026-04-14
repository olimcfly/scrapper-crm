<div class="auth-shell">
  <div class="auth-background-glow auth-background-glow-1"></div>
  <div class="auth-background-glow auth-background-glow-2"></div>

  <div class="auth-layout">
    <section class="auth-visual" aria-label="Présentation SCRAPPER CRM">
      <div class="auth-visual-inner">
        <span class="auth-badge">Prospection intelligente</span>

        <div class="auth-hero-copy">
          <h1 class="auth-brand">SCRAPPER CRM</h1>
          <p class="auth-tagline">
            Centralisez vos prospects, pilotez vos relances et gardez une vue claire
            sur votre activité depuis une interface pensée pour l’action.
          </p>
        </div>

        <div class="auth-feature-list">
          <div class="auth-feature-item">
            <span class="auth-feature-icon">✓</span>
            <div>
              <strong>Connexion rapide</strong>
              <p>Accès sécurisé par code unique, sans mot de passe à retenir.</p>
            </div>
          </div>

          <div class="auth-feature-item">
            <span class="auth-feature-icon">✓</span>
            <div>
              <strong>Pilotage clair</strong>
              <p>Prospects, pipeline et actions du jour dans un seul espace.</p>
            </div>
          </div>

          <div class="auth-feature-item">
            <span class="auth-feature-icon">✓</span>
            <div>
              <strong>Utilisable partout</strong>
              <p>Expérience plus propre, plus lisible et plus premium sur mobile.</p>
            </div>
          </div>
        </div>

        <div class="auth-kpi-panel">
          <div class="auth-kpi">
            <strong>24</strong>
            <span>Prospects actifs</span>
          </div>
          <div class="auth-kpi">
            <strong>11</strong>
            <span>Opportunités</span>
          </div>
          <div class="auth-kpi">
            <strong>7</strong>
            <span>Actions du jour</span>
          </div>
        </div>
      </div>
    </section>

    <section class="auth-panel">
      <div class="auth-card">
        <div class="auth-card-top">
          <p class="auth-overline">Accès sécurisé</p>
          <h2 class="auth-title">Connexion à votre espace</h2>
          <p class="auth-subtitle">
            Entrez votre email professionnel pour recevoir un code de connexion à usage unique.
          </p>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="auth-alert auth-alert-danger">
            <strong>Impossible de vous connecter</strong>
            <ul>
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="/login" novalidate class="auth-form">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

          <div class="auth-field">
            <label class="auth-label" for="email">Email professionnel</label>
            <input
              class="auth-input"
              id="email"
              type="email"
              name="email"
              autocomplete="email"
              required
              value="<?= htmlspecialchars((string) ($old['email'] ?? '')) ?>"
              placeholder="nom@entreprise.fr"
            >
          </div>

          <button type="submit" class="auth-button">
            Recevoir mon code de connexion
          </button>
        </form>

        <div class="auth-reassurance">
          <span class="auth-reassurance-dot"></span>
          <p>Authentification sans mot de passe · session sécurisée</p>
        </div>
      </div>
    </section>
  </div>
</div>