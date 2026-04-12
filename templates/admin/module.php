<div class="card" style="background:linear-gradient(135deg,#111827,#1e3a8a);color:#dbeafe;border:none;">
  <p class="eyebrow" style="color:#bfdbfe;">Module</p>
  <h2 style="margin:0 0 10px;"><?= htmlspecialchars((string) ($module['label'] ?? 'Module')) ?></h2>
  <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status'] ?? ''] ?? '')) ?>">
    <?= htmlspecialchars((string) ($statusLabels[$module['status'] ?? ''] ?? ($module['status'] ?? ''))) ?>
  </span>
  <p class="muted" style="margin:12px 0 0;color:#cbd5e1;max-width:700px;">
    <?= htmlspecialchars((string) ($module['description'] ?? '')) ?>
  </p>
</div>

<section class="card empty-guided">
  <p class="eyebrow">Bientôt disponible</p>
  <h3 style="margin:6px 0 8px;">Ce module est prêt côté navigation</h3>
  <p class="muted">La logique métier profonde sera branchée en phase suivante. En attendant, la route est stable et exploitable sur mobile comme desktop.</p>
  <div class="row">
    <div>
      <a class="btn" href="/dashboard">Retour dashboard</a>
    </div>
    <div>
      <a class="btn secondary" href="/prospects">Aller sur Collecte profils</a>
    </div>
  </div>
</section>
