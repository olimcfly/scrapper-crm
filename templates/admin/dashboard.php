<div class="card page-lead stack-sm">
  <p class="eyebrow">Phase 1 · Fondations MVP</p>
  <h2>Dashboard de démarrage mobile-first</h2>
  <p>Shell responsive actif, navigation stable mobile + desktop, et point d’entrée prospect-first sans écran cassé.</p>
</div>

<?php require __DIR__ . '/../components/global_states.php'; ?>

<div class="card stack-sm">
  <p class="muted">Aujourd’hui</p>
  <h3>Priorité : relancer 3 prospects chauds</h3>
  <p class="muted">Concentrez-vous sur la prochaine action à fort impact avant midi.</p>
  <?php
    $ctaLabel = 'Ouvrir la liste prospects';
    $ctaHref = '/prospects';
    require __DIR__ . '/../components/ui/primary_cta_button.php';
  ?>
</div>

<div class="row">
  <article class="dashboard-kpi-card">
    <p class="muted">Prospects actifs</p>
    <p class="metric-value">24</p>
  </article>
  <article class="dashboard-kpi-card">
    <p class="muted">Messages à envoyer</p>
    <p class="metric-value">7</p>
  </article>
  <article class="dashboard-kpi-card">
    <p class="muted">Opportunités pipeline</p>
    <p class="metric-value">11</p>
  </article>
</div>

<div class="card stack-sm">
  <h3>Quick actions</h3>
  <div class="quick-actions-grid">
    <a class="btn secondary" href="/prospects/create">+ Prospect</a>
    <a class="btn secondary" href="/strategie">Stratégie</a>
    <a class="btn secondary" href="/messages-ia">Messages IA</a>
    <a class="btn secondary" href="/pipeline">Pipeline</a>
  </div>
</div>

<div class="card stack-sm">
  <h3>Statut modules (phase 1)</h3>
  <div class="row">
    <div>
      <p class="muted">Actifs</p>
      <p class="metric-value"><?= (int) ($statusCounters['active'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted">MVP</p>
      <p class="metric-value"><?= (int) ($statusCounters['mvp'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted">Placeholders</p>
      <p class="metric-value"><?= (int) ($statusCounters['placeholder'] ?? 0) ?></p>
    </div>
  </div>
</div>
