<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>

  <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>

<section class="dashboard-shell stack-lg">
  <div class="dashboard-top-grid">
    <article class="card dashboard-hero">
      <p class="eyebrow">Focus du jour</p>
      <h1>Relancer 3 prospects chauds</h1>
      <p class="hero-text">
        Concentrez-vous sur les actions à fort impact pour faire avancer votre pipeline avant midi.
      </p>
      <div class="hero-actions">
        <a class="btn primary" href="/prospects?filter=hot">Ouvrir les prospects chauds</a>
        <a class="btn secondary" href="/pipeline">Voir le pipeline</a>
      </div>
    </article>

    <article class="card ai-card">
      <div class="card-header-inline">
        <h3>Recommandation IA</h3>
        <span class="badge badge-high">Priorité haute</span>
      </div>
      <p>
        Sophie Martin montre un signal fort : visite répétée, interaction récente et profil aligné.
      </p>
      <div class="hero-actions">
        <a class="btn secondary" href="/messages-ia">Préparer un message</a>
      </div>
    </article>
  </div>

  <section class="dashboard-kpis">
    <article class="dashboard-kpi-card">
      <p class="muted">Prospects actifs</p>
      <p class="metric-value">24</p>
      <p class="metric-trend positive">+5 cette semaine</p>
    </article>

    <article class="dashboard-kpi-card">
      <p class="muted">Messages à envoyer</p>
      <p class="metric-value">7</p>
      <p class="metric-trend">2 urgents aujourd’hui</p>
    </article>

    <article class="dashboard-kpi-card">
      <p class="muted">Opportunités pipeline</p>
      <p class="metric-value">11</p>
      <p class="metric-trend positive">3 proches conversion</p>
    </article>
  </section>

  <article class="card stack-sm">
    <div class="card-header-inline">
      <h3>Aperçu pipeline</h3>
      <a class="text-link" href="/pipeline">Ouvrir</a>
    </div>
    <div class="pipeline-preview-grid">
      <div class="pipeline-stage"><span>Nouveaux</span><strong>8</strong></div>
      <div class="pipeline-stage"><span>À contacter</span><strong>6</strong></div>
      <div class="pipeline-stage"><span>En échange</span><strong>4</strong></div>
      <div class="pipeline-stage"><span>Chauds</span><strong>3</strong></div>
      <div class="pipeline-stage"><span>Gagnés</span><strong>2</strong></div>
    </div>
  </article>

  <article class="card stack-sm">
    <h3>Actions rapides</h3>
    <div class="quick-actions-grid premium-actions">
      <a class="action-tile" href="/prospects/create">+ Prospect</a>
      <a class="action-tile" href="/strategie">Stratégie</a>
      <a class="action-tile" href="/messages-ia">Messages IA</a>
      <a class="action-tile" href="/pipeline">Pipeline</a>
    </div>
  </article>

  <article class="card stack-sm">
    <h3>Statut des modules</h3>
    <div class="dashboard-kpis">
      <div class="dashboard-kpi-card">
        <p class="muted">Actifs</p>
        <p class="metric-value"><?= (int) ($statusCounters['active'] ?? 0) ?></p>
      </div>
      <div class="dashboard-kpi-card">
        <p class="muted">MVP</p>
        <p class="metric-value"><?= (int) ($statusCounters['mvp'] ?? 0) ?></p>
      </div>
      <div class="dashboard-kpi-card">
        <p class="muted">Placeholders</p>
        <p class="metric-value"><?= (int) ($statusCounters['placeholder'] ?? 0) ?></p>
      </div>
    </div>
  </article>
</section>