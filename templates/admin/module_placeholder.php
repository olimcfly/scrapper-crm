<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <h2 style="margin:0;"><?= htmlspecialchars((string) ($module['icon'] ?? '📦')) ?> <?= htmlspecialchars((string) ($module['label'] ?? 'Module')) ?></h2>
    <span class="badge <?= ($module['status'] ?? '') === 'Actif' ? 'actif' : (($module['status'] ?? '') === 'Bêta' ? 'beta' : 'dev') ?>"><?= htmlspecialchars((string) ($module['status'] ?? 'En cours de développement')) ?></span>
  </div>
  <p class="muted"><?= htmlspecialchars((string) ($module['description'] ?? 'Ce module est en préparation.')) ?></p>
</div>

<div class="card">
  <?php if (($isPlaceholderRoute ?? true) === true): ?>
    <h3 style="margin-top:0;">Page placeholder opérationnelle</h3>
    <p>Ce module n'est pas encore finalisé, mais la page est disponible pour garantir une navigation stable et cohérente dans l'admin.</p>
    <ul>
      <li>Accès sécurisé via les routes admin existantes.</li>
      <li>Aucun écran vide ni erreur 404 tant que le module est annoncé dans la sidebar.</li>
      <li>Statut produit visible (Actif, Bêta, En cours de développement).</li>
    </ul>
    <a href="/admin" class="btn">Retour dashboard</a>
  <?php else: ?>
    <h3 style="margin-top:0;">Module disponible</h3>
    <p>Ce module est déjà connecté à une page fonctionnelle du CRM. Utilisez la navigation latérale pour revenir rapidement à l'administration globale.</p>
    <a href="<?= htmlspecialchars((string) ($module['route'] ?? '/admin')) ?>" class="btn">Ouvrir le module</a>
  <?php endif; ?>
</div>

<div class="card">
  <h3 style="margin-top:0;">Hiérarchie cœur produit</h3>
  <div class="row">
    <?php foreach (($coreModules ?? []) as $core): ?>
      <div style="border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
        <strong><?= htmlspecialchars((string) $core['label']) ?></strong>
        <p class="muted" style="margin:6px 0 0;"><?= htmlspecialchars((string) $core['description']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>
