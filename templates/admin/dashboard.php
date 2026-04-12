<div class="card" style="background:linear-gradient(140deg,#0f172a 0%,#1d4ed8 100%);color:#dbeafe;border:none;">
  <p style="text-transform:uppercase;letter-spacing:.08em;font-size:12px;margin:0 0 8px;">Phase 1 · Fondations MVP</p>
  <h2 style="margin:0 0 8px;color:#fff;">Dashboard de démarrage mobile-first</h2>
  <p style="margin:0;max-width:780px;">Shell responsive actif, navigation stable mobile + desktop, et point d’entrée prospect-first sans écran cassé.</p>
</div>

<?php require __DIR__ . '/../components/global_states.php'; ?>

<div class="card">
  <p class="muted" style="margin:0 0 6px;">Aujourd’hui</p>
  <h3 style="margin:0 0 8px;">Priorité: relancer 3 prospects chauds</h3>
  <p class="muted" style="margin:0 0 16px;">Concentrez-vous sur la prochaine action à fort impact avant midi.</p>
  <?php
    $ctaLabel = 'Ouvrir la liste prospects';
    $ctaHref = '/prospects';
    require __DIR__ . '/../components/ui/primary_cta_button.php';
  ?>
</div>

<div class="row">
  <article class="card">
    <p class="muted" style="margin:0;">Prospects actifs</p>
    <p style="font-size:26px;font-weight:700;margin:6px 0 0;">24</p>
  </article>
  <article class="card">
    <p class="muted" style="margin:0;">Messages à envoyer</p>
    <p style="font-size:26px;font-weight:700;margin:6px 0 0;">7</p>
  </article>
  <article class="card">
    <p class="muted" style="margin:0;">Opportunités pipeline</p>
    <p style="font-size:26px;font-weight:700;margin:6px 0 0;">11</p>
  </article>
</div>

<div class="card">
  <h3 style="margin-top:0;">Quick actions</h3>
  <div class="row">
    <a class="btn secondary" href="/prospects/create">+ Prospect</a>
    <a class="btn secondary" href="/strategie">Stratégie</a>
    <a class="btn secondary" href="/messages-ia">Messages IA</a>
    <a class="btn secondary" href="/pipeline">Pipeline</a>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Statut modules (phase 1)</h3>
  <div class="row">
    <div>
      <p class="muted" style="margin:0;">Actifs</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['active'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted" style="margin:0;">MVP</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['mvp'] ?? 0) ?></p>
    </div>
    <div>
      <p class="muted" style="margin:0;">Placeholders</p>
      <p style="font-size:24px;font-weight:700;margin:4px 0 0;"><?= (int) ($statusCounters['placeholder'] ?? 0) ?></p>
    </div>
  </div>
</div>
