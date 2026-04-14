<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Configuration de l’espace CRM</h1>
      <p class="subtitle">
        Centralise les préférences, modules et accès de ton équipe
      </p>
    </div>

    <!-- HERO -->
    <div class="card premium-hero">
      <p class="eyebrow">Configuration</p>
      <h2>Paramètres de l’espace</h2>
      <p class="muted">
        Module en cours de déploiement. Les fonctionnalités avancées arrivent.
      </p>
    </div>

    <!-- EMPTY STATE -->
    <div class="card">
      <?php
        $emptyTitle = 'Paramètres avancés bientôt disponibles';
        $emptyDescription = 'La navigation est prête. Les options de personnalisation arrivent en Phase 2.';
        $emptyCtaLabel = 'Retour au dashboard';
        $emptyCtaHref = '/dashboard';
        require __DIR__ . '/../components/states/empty_state_guided.php';
      ?>
    </div>

  </div>
</div>