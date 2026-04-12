<div class="card" style="background:linear-gradient(140deg,#0f172a 0%,#1d4ed8 100%);color:#dbeafe;border:none;">
  <p style="text-transform:uppercase;letter-spacing:.08em;font-size:12px;margin:0 0 8px;">Phase 1 · Fondations MVP</p>
  <h2 style="margin:0 0 8px;color:#fff;">Dashboard mobile-first de démarrage</h2>
  <p style="margin:0;max-width:780px;">Structure lisible et navigable : priorités du jour, KPI courts, quick actions et accès direct aux pages clés.</p>
</div>

<?php
  $showLoading = false;
  $showEmpty = false;
  $showError = false;
  require __DIR__ . '/../components/global_states.php';
?>

<section class="card">
  <p class="muted" style="margin-top:0;">Aujourd’hui</p>
  <h3 style="margin:0 0 8px;">3 actions prioritaires à lancer</h3>
  <ul style="margin:0;padding-left:20px;">
    <li>Relancer les prospects chauds sans interaction depuis 7 jours.</li>
    <li>Ouvrir la stratégie de 2 prospects à score élevé.</li>
    <li>Préparer 5 messages IA de relance.</li>
  </ul>
</section>

<section class="card">
  <h3 style="margin-top:0;">KPI rapides</h3>
  <div class="dashboard-kpi">
    <div class="kpi-card"><p class="muted" style="margin:0;">Prospects chauds</p><p class="kpi-value">12</p></div>
    <div class="kpi-card"><p class="muted" style="margin:0;">Relances à faire</p><p class="kpi-value">7</p></div>
    <div class="kpi-card"><p class="muted" style="margin:0;">Messages en attente</p><p class="kpi-value">4</p></div>
    <div class="kpi-card"><p class="muted" style="margin:0;">Moves pipeline</p><p class="kpi-value">3</p></div>
  </div>
</section>

<section class="card">
  <h3 style="margin-top:0;">Quick actions</h3>
  <div class="quick-actions">
    <?php $href = '/prospects'; $label = 'Ouvrir Prospects'; $variant = 'primary'; require __DIR__ . '/../components/primary_cta_button.php'; ?>
    <?php $href = '/admin/modules/strategie-prospect'; $label = 'Lancer Stratégie'; $variant = 'secondary'; require __DIR__ . '/../components/primary_cta_button.php'; ?>
    <?php $href = '/admin/modules/messages-ia'; $label = 'Préparer Messages IA'; $variant = 'secondary'; require __DIR__ . '/../components/primary_cta_button.php'; ?>
    <?php $href = '/admin/modules/pipeline'; $label = 'Ouvrir Pipeline'; $variant = 'secondary'; require __DIR__ . '/../components/primary_cta_button.php'; ?>
  </div>
</section>

<section class="card">
  <h3 style="margin-top:0;">Modules accessibles (sans route cassée)</h3>
  <p class="muted">Les modules non prêts restent en placeholder premium pour conserver une navigation stable.</p>
  <div class="row">
    <a class="btn secondary" href="/admin/dashboard">Dashboard</a>
    <a class="btn secondary" href="/prospects">Prospects</a>
    <a class="btn secondary" href="/admin/modules/strategie-prospect">Stratégie</a>
    <a class="btn secondary" href="/admin/modules/messages-ia">Messages IA</a>
    <a class="btn secondary" href="/admin/modules/pipeline">Pipeline</a>
    <a class="btn secondary" href="/settings">Paramètres</a>
  </div>
</section>
