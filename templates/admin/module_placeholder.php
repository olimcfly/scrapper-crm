<div class="card module-summary-card">
  <div class="module-summary-header">
    <h2 class="module-summary-title">
      <?= htmlspecialchars((string) ($module['icon'] ?? '📦')) ?>
      <?= htmlspecialchars((string) ($module['label'] ?? 'Module')) ?>
    </h2>

    <?php
      $moduleStatus = (string) ($module['status'] ?? 'placeholder');
      $moduleStatusLabel = $statusLabels[$moduleStatus] ?? 'En cours de développement';
      $moduleStatusClass = $statusClassMap[$moduleStatus] ?? 'status-placeholder';
    ?>
    <span class="badge status-badge <?= htmlspecialchars($moduleStatusClass) ?>">
      <?= htmlspecialchars((string) $moduleStatusLabel) ?>
    </span>
  </div>

  <p class="muted module-summary-text">
    <?= htmlspecialchars((string) ($module['description'] ?? 'Ce module est en préparation.')) ?>
  </p>
</div>

<div class="card module-placeholder-card">
  <?php if (($isPlaceholderRoute ?? true) === true): ?>
    <h3 class="card-title">Page placeholder opérationnelle</h3>
    <p class="module-placeholder-text">
      Ce module n'est pas encore finalisé, mais la page est disponible pour garantir une navigation stable et cohérente dans l'admin.
    </p>

    <ul class="module-placeholder-list">
      <li>Accès sécurisé via les routes admin existantes.</li>
      <li>Aucun écran vide ni erreur 404 tant que le module est annoncé dans la sidebar.</li>
      <li>Statut produit visible : Actif, MVP, Placeholder.</li>
    </ul>

    <a href="/admin" class="btn secondary">Retour dashboard</a>
  <?php else: ?>
    <h3 class="card-title">Module disponible</h3>
    <p class="module-placeholder-text">
      Ce module est déjà connecté à une page fonctionnelle du CRM. Utilisez la navigation latérale pour revenir rapidement à l'administration globale.
    </p>

    <a href="<?= htmlspecialchars((string) ($module['route'] ?? '/admin')) ?>" class="btn primary-cta-button">
      Ouvrir le module
    </a>
  <?php endif; ?>
</div>

<div class="card core-hierarchy-card">
  <h3 class="card-title">Hiérarchie cœur produit</h3>

  <div class="core-modules-grid">
    <?php foreach (($coreModules ?? []) as $core): ?>
      <div class="core-module-item">
        <strong class="core-module-title">
          <?= htmlspecialchars((string) $core['label']) ?>
        </strong>
        <p class="muted core-module-text">
          <?= htmlspecialchars((string) $core['description']) ?>
        </p>
      </div>
    <?php endforeach; ?>
  </div>
</div>