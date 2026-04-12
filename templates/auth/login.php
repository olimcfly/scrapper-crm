<div class="card" style="max-width:460px;margin:40px auto;padding:24px;border-radius:12px;">
  <h2 style="margin-top:0;margin-bottom:8px;">Connexion</h2>
  <p style="margin-top:0;color:#6b7280;margin-bottom:20px;">Accédez à votre espace CRM en toute sécurité.</p>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <strong>Impossible de vous connecter :</strong>
      <ul style="margin:8px 0 0 20px;padding:0;">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/login" novalidate>
    <div style="margin-bottom:12px;">
      <label for="email" style="display:block;font-weight:600;margin-bottom:6px;">Email professionnel</label>
      <input
        id="email"
        type="email"
        name="email"
        autocomplete="email"
        required
        value="<?= htmlspecialchars((string) ($old['email'] ?? '')) ?>"
      >
    </div>

    <div style="margin-bottom:16px;">
      <label for="password" style="display:block;font-weight:600;margin-bottom:6px;">Mot de passe</label>
      <input
        id="password"
        type="password"
        name="password"
        autocomplete="current-password"
        required
      >
    </div>

    <button type="submit" class="btn" style="width:100%;padding:10px 12px;font-weight:600;">Se connecter</button>
  </form>
</div>
