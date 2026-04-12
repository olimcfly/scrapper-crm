<div class="card" style="background:linear-gradient(140deg,#0f172a 0%,#1d4ed8 100%);color:#dbeafe;border:none;">
  <p class="eyebrow" style="color:#bfdbfe;">Phase 1 · Fondations MVP</p>
  <h2 style="margin:0 0 8px;color:#fff;">Dashboard de démarrage</h2>
  <p style="margin:0;max-width:780px;">Structure mobile-first prête : navigation au pouce, routes stables et premiers repères d’exécution prospect-first.</p>
</div>

<?php require __DIR__ . '/../components/global_states.php'; ?>

<section class="card">
  <p class="eyebrow">Aujourd’hui</p>
  <h3 style="margin:0 0 8px;">Priorité : préparer les prochaines relances</h3>
  <p class="muted" style="margin:0;">Passez par Prospects puis Stratégie pour garder un flux simple : prospect → angle → message.</p>
</section>

<section class="row">
  <article class="card">
    <p class="muted" style="margin-top:0;">Prospects chauds</p>
    <p style="font-size:30px;font-weight:700;margin:4px 0;">12</p>
    <p class="muted" style="margin-bottom:0;">à relancer dans les 24h</p>
  </article>
  <article class="card">
    <p class="muted" style="margin-top:0;">Messages en attente</p>
    <p style="font-size:30px;font-weight:700;margin:4px 0;">5</p>
    <p class="muted" style="margin-bottom:0;">brouillons IA à valider</p>
  </article>
  <article class="card">
    <p class="muted" style="margin-top:0;">Pipeline à bouger</p>
    <p style="font-size:30px;font-weight:700;margin:4px 0;">3</p>
    <p class="muted" style="margin-bottom:0;">opportunités bloquées</p>
  </article>
</section>

<section class="card">
  <p class="eyebrow">Quick actions</p>
  <div class="row">
    <div>
      <?php $label = 'Ouvrir Prospects'; $href = '/prospects'; $icon = '👥'; include __DIR__ . '/../components/PrimaryCTAButton.php'; ?>
    </div>
    <div>
      <?php $label = 'Aller à Stratégie'; $href = '/strategie'; $icon = '🎯'; include __DIR__ . '/../components/PrimaryCTAButton.php'; ?>
    </div>
    <div>
      <?php $label = 'Préparer message IA'; $href = '/messages-ia'; $icon = '💬'; include __DIR__ . '/../components/PrimaryCTAButton.php'; ?>
    </div>
  </div>
</section>

<section class="card">
  <p class="eyebrow">Tests UX phase 1</p>
  <div class="row">
    <a class="btn secondary" href="/admin/dashboard?state=loading">Voir loading</a>
    <a class="btn secondary" href="/admin/dashboard?state=empty">Voir empty state</a>
    <a class="btn secondary" href="/admin/dashboard?state=error">Voir erreur</a>
    <a class="btn secondary" href="/admin/dashboard">Reset vue</a>
  </div>
</section>
