<style>
  .login-page {
    min-height: 100dvh;
    display: grid;
    grid-template-columns: 1fr;
    background: radial-gradient(circle at top right, #e9efff 0%, #f3f6fb 40%, #eef2f7 100%);
  }

  .login-visual {
    display: none;
    position: relative;
    min-height: 100dvh;
    padding: clamp(34px, 4vw, 56px);
    color: #dbeafe;
    background: linear-gradient(165deg, #0f172a 0%, #1e3a8a 42%, #2563eb 100%);
    overflow: hidden;
  }

  .login-visual::after {
    content: '';
    position: absolute;
    width: 360px;
    height: 360px;
    border-radius: 999px;
    right: -120px;
    bottom: -120px;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.28), transparent 66%);
  }

  .login-visual-content {
    position: relative;
    z-index: 1;
    display: grid;
    gap: 16px;
    align-self: end;
    max-width: 520px;
  }

  .login-brand {
    margin: 0;
    font-size: clamp(1.8rem, 2.8vw, 2.7rem);
    font-weight: 700;
    color: #fff;
  }

  .login-tagline {
    color: rgba(219, 234, 254, 0.9);
    font-size: 1rem;
    line-height: 1.55;
  }

  .login-kpi-grid {
    margin-top: 8px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
  }

  .login-kpi {
    background: rgba(15, 23, 42, 0.33);
    border: 1px solid rgba(191, 219, 254, 0.22);
    border-radius: 12px;
    padding: 10px;
  }

  .login-kpi strong {
    display: block;
    color: #fff;
    font-size: 1.05rem;
  }

  .login-kpi span {
    font-size: .8rem;
    color: rgba(219, 234, 254, 0.9);
  }

  .login-form-area {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(18px, 4vw, 56px);
  }

  .login-card {
    width: 100%;
    max-width: 460px;
    padding: clamp(24px, 3.6vw, 34px);
    border-radius: 20px;
    background: #fff;
    border: 1px solid #dbe3ee;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
  }

  .login-overline {
    margin: 0;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: .08em;
    color: #2563eb;
    font-weight: 700;
  }

  .login-title {
    margin: 10px 0 8px;
    font-size: clamp(1.7rem, 2.4vw, 2.1rem);
    color: #0f172a;
    line-height: 1.15;
  }

  .login-subtitle {
    margin: 0 0 16px;
    color: #64748b;
    line-height: 1.55;
    font-size: .95rem;
  }

  .login-email-hint {
    margin: 0 0 20px;
    padding: 10px 14px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    color: #1e3a8a;
    font-size: .9rem;
  }

  .login-field { margin-bottom: 16px; }

  .login-label {
    display: block;
    margin-bottom: 7px;
    color: #1e293b;
    font-weight: 600;
    font-size: 0.92rem;
  }

  .login-input {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #fff;
    padding: 12px 14px;
    font-size: 1.35rem;
    letter-spacing: 6px;
    color: #0f172a;
    text-align: center;
    font-family: monospace;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
  }

  .login-input:focus {
    outline: none;
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
  }

  .login-submit {
    width: 100%;
    margin-top: 6px;
    border: none;
    border-radius: 12px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    font-size: .98rem;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.16s ease, filter 0.16s ease;
  }

  .login-submit:hover { filter: brightness(1.03); transform: translateY(-1px); }

  .login-back {
    display: block;
    margin-top: 14px;
    text-align: center;
    font-size: .88rem;
    color: #2563eb;
    text-decoration: none;
  }

  .login-back:hover { text-decoration: underline; }

  .login-footnote {
    margin: 16px 0 0;
    text-align: center;
    font-size: 0.85rem;
    color: #64748b;
  }

  .login-errors {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 16px;
  }

  .login-errors ul { margin: 8px 0 0 18px; padding: 0; }

  @media (min-width: 980px) {
    .login-page { grid-template-columns: minmax(460px, 1fr) minmax(520px, 1fr); }
    .login-visual { display: grid; }
  }
</style>

<div class="login-page">
  <section class="login-visual" aria-label="Présentation SCRAPPER CRM">
    <div class="login-visual-content">
      <h1 class="login-brand">SCRAPPER CRM</h1>
      <p class="login-tagline">Centralisez vos prospects, pilotez le pipeline et sécurisez vos relances depuis une seule interface.</p>
      <div class="login-kpi-grid">
        <div class="login-kpi"><strong>24</strong><span>Prospects actifs</span></div>
        <div class="login-kpi"><strong>11</strong><span>Opportunités</span></div>
        <div class="login-kpi"><strong>7</strong><span>Actions du jour</span></div>
      </div>
    </div>
  </section>

  <section class="login-form-area">
    <div class="login-card">
      <p class="login-overline">Accès sécurisé</p>
      <h2 class="login-title">Code de vérification</h2>
      <p class="login-subtitle">Un code à 6 chiffres a été envoyé à votre adresse email.</p>

      <p class="login-email-hint">
        Code envoyé à <strong><?= htmlspecialchars($email ?? '') ?></strong>
      </p>

      <?php if (!empty($errors)): ?>
        <div class="login-errors">
          <strong>Erreur :</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/login/verify" novalidate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
        <div class="login-field">
          <label class="login-label" for="code">Code à 6 chiffres</label>
          <input
            class="login-input"
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

        <button type="submit" class="login-submit">Valider et accéder</button>
      </form>

      <a class="login-back" href="/login">← Renvoyer un nouveau code</a>

      <p class="login-footnote">Authentification sans mot de passe · session sécurisée</p>
    </div>
  </section>
</div>
