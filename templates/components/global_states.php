<?php
$showLoading = (bool) ($showLoading ?? false);
$showEmpty = (bool) ($showEmpty ?? false);
$showError = (bool) ($showError ?? false);
?>

<?php if ($showLoading): ?>
  <?php require __DIR__ . '/states/loading_skeleton_card.php'; ?>
<?php endif; ?>

<?php if ($showEmpty): ?>
  <?php
    $emptyTitle = 'Aucune donnée disponible';
    $emptyDescription = 'Commencez par lancer une collecte pour alimenter votre base de prospection.';
    $emptyCtaLabel = 'Lancer une collecte';
    $emptyCtaHref = '/prospects/sources';
    require __DIR__ . '/states/empty_state_guided.php';
  ?>
<?php endif; ?>

<?php if ($showError): ?>
  <section class="global-state global-error" role="alert">
    
    <span class="state-dot"></span>

    <div class="state-content">
      <strong class="state-title">Erreur de chargement</strong>
      <div class="state-message">
        Vérifiez la connexion puis relancez l’action principale.
      </div>
    </div>

  </section>
<?php endif; ?>
