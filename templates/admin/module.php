<div class="card premium-hero">
  <p class="eyebrow">Module</p>
  <h2 style="margin-bottom:8px;"><?= htmlspecialchars((string) ($module['label'] ?? 'Module')) ?></h2>
  <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status'] ?? ''] ?? '')) ?>">
    <?= htmlspecialchars((string) ($statusLabels[$module['status'] ?? ''] ?? ($module['status'] ?? ''))) ?>
  </span>
  <p class="muted" style="margin-top:14px;max-width:780px;">
    <?= htmlspecialchars((string) ($module['description'] ?? '')) ?>
  </p>
</div>

<div class="card">
  <h3 style="margin-top:0;">Placeholder fonctionnel</h3>
  <p class="muted">
    Ce module n'est pas encore finalisé. Cette page garantit une navigation stable dans l'admin :
    aucune route vide, aucune redirection cassée.
  </p>
  <div class="row">
    <div>
      <a class="btn" href="/dashboard">Retour dashboard</a>
    </div>
    <div>
      <a class="btn secondary" href="/prospects">Aller sur Collecte profils</a>
    </div>
  </div>
</div>
