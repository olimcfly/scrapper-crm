<div id="welcome-popup" class="welcome-popup hidden" role="dialog" aria-modal="true" aria-labelledby="welcome-title">
  <div class="welcome-card">
    <button class="welcome-close" type="button" onclick="closeWelcome()" aria-label="Fermer la fenêtre de bienvenue">×</button>

    <div class="welcome-content">
      <h2 id="welcome-title">Bonjour Coralie 👋</h2>

      <p class="welcome-quote">
        "Le succès ne vient pas de ce que vous faites de temps en temps,
        mais de ce que vous faites chaque jour."
      </p>

      <button class="btn-primary" type="button" onclick="closeWelcome()">
        Commencer ma journée
      </button>
    </div>
  </div>
</div>

<section class="dashboard-premium">
  <header class="dashboard-hero card">
    <div class="dashboard-hero-main">
      <p class="eyebrow">Prospection first</p>
      <h1>Lancez votre prochaine vague de prospection</h1>
      <p class="dashboard-hero-text">Collectez vite des prospects qualifiés, priorisez les plus chauds et passez à l’action commerciale immédiatement.</p>

      <div class="dashboard-hero-cta">
        <a class="btn btn-primary hero-primary-cta" href="/prospects/sources">Trouver des prospects</a>
        <a class="btn btn-secondary" href="/prospects?filter=hot">Voir les prospects chauds</a>
      </div>
    </div>

    <aside class="dashboard-hero-panel" aria-label="Focus conversion">
      <p class="dashboard-panel-label">Collection intelligente</p>
      <p class="dashboard-panel-value">24 prospects</p>
      <p class="dashboard-panel-text"><strong>9</strong> à fort potentiel · <strong>6</strong> à analyser en priorité.</p>
      <a class="dashboard-panel-link" href="/prospects">Voir la collection →</a>
    </aside>
  </header>

  <section class="card dashboard-collection-focus" aria-label="Collection intelligente de prospects">
    <div class="card-header-inline">
      <h3>Collection intelligente</h3>
      <a class="text-link" href="/prospects">Voir la collection</a>
    </div>
    <p class="dashboard-collection-text">Votre base active est prête pour la prospection : ciblez les contacts à fort potentiel et accélérez l’analyse commerciale.</p>
    <div class="dashboard-kpis dashboard-collection-kpis">
      <article class="dashboard-kpi-card kpi-primary">
        <p class="muted">Total prospects</p>
        <p class="metric-value">24</p>
        <p class="metric-trend positive">Base active</p>
      </article>

      <article class="dashboard-kpi-card">
        <p class="muted">Fort potentiel</p>
        <p class="metric-value">9</p>
        <p class="metric-trend positive">À contacter vite</p>
      </article>

      <article class="dashboard-kpi-card">
        <p class="muted">À analyser</p>
        <p class="metric-value">6</p>
        <p class="metric-trend">Avec analyse IA</p>
      </article>
    </div>
    <a class="btn btn-secondary dashboard-collection-cta" href="/prospects">Voir la collection</a>
  </section>

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
  </section>


  <section class="card dashboard-decision-center" aria-label="Centre de décisions">
    <div class="card-header-inline">
      <h3>Centre de décisions</h3>
      <a class="text-link" href="/pipeline">Voir tout le pipeline</a>
    </div>
    <div class="dashboard-decision-grid">
      <article class="decision-item decision-priority">
        <p class="decision-label">Priorité #1</p>
        <strong>Relancer 4 prospects en retard avant 17h</strong>
        <p>Impact estimé : +2 rendez-vous cette semaine.</p>
      </article>
      <article class="decision-item">
        <p class="decision-label">Priorité #2</p>
        <strong>Basculer 3 prospects chauds en étape “Proposition”</strong>
        <p>Action rapide depuis le pipeline mobile.</p>
      </article>
      <article class="decision-item ai-suggestion-block">
        <p class="ai-suggestion-title">Suggestion IA</p>
        <p>Je te recommande de démarrer par Sophie Martin : elle a ouvert ton dernier message il y a moins de 24h.</p>
        <a class="btn btn-secondary" href="/messages-ia?type=relance">Rédiger une relance</a>
      </article>
    </div>
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
        <a class="action-tile" href="/prospects/create"><strong>Ajouter un prospect</strong><span>Saisie manuelle</span></a>
      </div>
    </article>

    <aside class="dashboard-side-stack">
      <section class="card stack-sm dashboard-recent-card mobile-secondary-block" aria-label="Prospects récents">
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

      <article class="card stack-sm dashboard-modules-card mobile-secondary-block">
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

<script>
  (function () {
    const popup = document.getElementById('welcome-popup');
    const shouldShowWelcome = <?= !empty($showWelcomePopup) ? 'true' : 'false' ?>;

    if (!popup || !shouldShowWelcome) {
      return;
    }

    popup.classList.remove('hidden');
  })();

  function closeWelcome() {
    const popup = document.getElementById('welcome-popup');

    if (!popup) {
      return;
    }

    popup.classList.add('hidden');
  }
</script>
