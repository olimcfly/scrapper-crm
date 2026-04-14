<section class="dashboard-mobile-first stack-lg">
  <header class="dashboard-mobile-header card">
    <p class="eyebrow">Dashboard</p>
    <h1>Priorités du jour</h1>
    <p class="muted">Une vue claire pour agir vite depuis votre téléphone.</p>

    <div class="dashboard-header-cta">
      <a class="btn btn-primary" href="/prospects/create">+ Ajouter un prospect</a>
      <a class="btn btn-secondary" href="/prospects?filter=hot">Voir les prospects chauds</a>
    </div>
  </header>

  <section class="dashboard-kpis" aria-label="Indicateurs clés">
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

  <section class="card stack-sm dashboard-day-actions" aria-label="Actions rapides du jour">
    <div class="card-header-inline">
      <h3>Actions du jour</h3>
      <a class="text-link" href="/pipeline">Pipeline</a>
    </div>

    <div class="quick-actions-grid premium-actions">
      <a class="action-tile" href="/prospects?filter=hot">Relancer les chauds</a>
      <a class="action-tile" href="/messages-ia">Préparer un message IA</a>
      <a class="action-tile" href="/strategie">Mettre à jour la stratégie</a>
      <a class="action-tile" href="/prospects/create">Créer un prospect</a>
    </div>
  </section>

  <section class="card stack-sm" aria-label="Prospects récents">
    <div class="card-header-inline">
      <h3>Prospects récents</h3>
      <a class="text-link" href="/prospects">Voir tout</a>
    </div>

    <div class="dashboard-list">
      <article class="dashboard-list-item">
        <div class="dashboard-list-item-main">
          <p class="dashboard-list-item-title">Sophie Martin</p>
          <p class="dashboard-list-item-text">Coach bien-être · Lyon</p>
        </div>
        <a class="btn btn-secondary" href="/prospects?filter=hot">Ouvrir</a>
      </article>

      <article class="dashboard-list-item">
        <div class="dashboard-list-item-main">
          <p class="dashboard-list-item-title">Espace Harmonie</p>
          <p class="dashboard-list-item-text">Praticienne SPA · Nantes</p>
        </div>
        <a class="btn btn-secondary" href="/pipeline">Suivre</a>
      </article>

      <article class="dashboard-list-item">
        <div class="dashboard-list-item-main">
          <p class="dashboard-list-item-title">Julie Bernard</p>
          <p class="dashboard-list-item-text">Naturopathe · Bordeaux</p>
        </div>
        <a class="btn btn-secondary" href="/messages-ia">Contacter</a>
      </article>
    </div>
  </section>

  <article class="card stack-sm">
    <h3>Statut des modules</h3>
    <div class="dashboard-kpis dashboard-kpis-modules">
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
