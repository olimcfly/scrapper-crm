<section class="dashboard-premium">
  <header class="dashboard-hero card">
    <div class="dashboard-hero-main">
      <p class="eyebrow">Cockpit CRM</p>
      <h1>Priorités commerciales du jour</h1>
      <p class="dashboard-hero-text">Pilotez la prospection, les relances et le pipeline depuis une vue produit claire, rapide et orientée action.</p>

      <div class="dashboard-hero-cta">
        <a class="btn btn-primary" href="/prospects/create">+ Ajouter un prospect</a>
        <a class="btn btn-secondary" href="/prospects?filter=hot">Voir les prospects chauds</a>
      </div>
    </div>

    <aside class="dashboard-hero-panel" aria-label="Focus conversion">
      <p class="dashboard-panel-label">Focus conversion</p>
      <p class="dashboard-panel-value">3 opportunités</p>
      <p class="dashboard-panel-text">à moins de 7 jours de signature selon l’activité pipeline.</p>
      <a class="dashboard-panel-link" href="/pipeline">Ouvrir le pipeline →</a>
    </aside>
  </header>

  <section class="dashboard-kpis" aria-label="Indicateurs clés">
    <article class="dashboard-kpi-card kpi-primary">
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

    <article class="dashboard-kpi-card">
      <p class="muted">Relances en retard</p>
      <p class="metric-value">4</p>
      <p class="metric-trend">À traiter avant 17h</p>
    </article>
  </section>

  <section class="dashboard-workspace" aria-label="Espace de travail principal">
    <article class="card dashboard-main-flow">
      <div class="card-header-inline">
        <h3>Actions à fort impact</h3>
        <a class="text-link" href="/messages-ia">Ouvrir Messages IA</a>
      </div>

      <div class="quick-actions-grid premium-actions">
        <a class="action-tile" href="/prospects?filter=hot"><strong>Relancer les chauds</strong><span>Priorité haute</span></a>
        <a class="action-tile" href="/messages-ia"><strong>Préparer un message IA</strong><span>Script personnalisé</span></a>
        <a class="action-tile" href="/strategie"><strong>Mettre à jour la stratégie</strong><span>Angles et hooks</span></a>
        <a class="action-tile" href="/prospects/create"><strong>Créer un prospect</strong><span>Entrée rapide</span></a>
      </div>
    </article>

    <aside class="dashboard-side-stack">
      <section class="card stack-sm dashboard-recent-card" aria-label="Prospects récents">
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

      <article class="card stack-sm dashboard-modules-card">
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
    </aside>
  </section>
</section>
