<div class="card" style="background:linear-gradient(140deg,#0f172a 0%,#1d4ed8 100%);color:#dbeafe;border:none;">
  <p style="text-transform:uppercase;letter-spacing:.08em;font-size:12px;margin:0 0 8px;">Phase 1 · Fondations MVP</p>
  <h2 style="margin:0 0 8px;color:#fff;">Dashboard de démarrage mobile-first</h2>
  <p style="margin:0;max-width:780px;">Vue minimale pour lancer l’exécution : navigation stable, prochaines actions, et modules MVP accessibles sans route cassée.</p>
</div>

<?php
  $showLoading = false;
  $showEmpty = false;
  $showError = false;
  require __DIR__ . '/../components/global_states.php';
?>

<div class="row">
  <article class="card">
    <p class="muted" style="margin-top:0;">Prochaine meilleure action</p>
    <h3 style="margin:0 0 8px;">Relancer les prospects chauds</h3>
    <p class="muted">Ouvrir la liste prospects et filtrer par score élevé / dernière interaction > 7 jours.</p>
    <a class="btn" href="/prospects">Ouvrir Prospects</a>
  </article>

  <article class="card">
    <p class="muted" style="margin-top:0;">Pivot produit</p>
    <h3 style="margin:0 0 8px;">Stratégie par prospect</h3>
    <p class="muted">Définir l’angle, l’objectif et la prochaine action IA depuis une fiche prospect.</p>
    <a class="btn secondary" href="/admin/modules/strategie-prospect">Ouvrir Stratégie</a>
  </article>
</div>

<div class="card">
  <h3 style="margin-top:0;">Navigation MVP (mobile + desktop)</h3>
  <div class="row">
    <a class="btn secondary" href="/admin/dashboard">Dashboard</a>
    <a class="btn secondary" href="/prospects">Prospects</a>
    <a class="btn secondary" href="/admin/modules/messages-ia">Messages IA</a>
    <a class="btn secondary" href="/admin/modules/pipeline">Pipeline</a>
    <a class="btn secondary" href="/admin/modules/contacts">Contacts</a>
  </div>
  <p class="muted" style="margin-bottom:0;">Les modules complexes restent en placeholder pour cette phase.</p>
</div>

<div class="card">
  <h3 style="margin-top:0;">Statut modules (phase 1)</h3>
  <div class="row">
    <div>
      <p class="muted" style="margin:0;">Actifs</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['active'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted" style="margin:0;">Bêta</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['beta'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted" style="margin:0;">En développement</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['in_progress'] ?? 0) ?></p>
    </div>
  </div>
</div>
