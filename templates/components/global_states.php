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
    $emptyDescription = 'Commencez par ajouter un prospect pour activer le flux prospect-first.';
    $emptyCtaLabel = 'Créer un prospect';
    $emptyCtaHref = '/prospects/create';
    require __DIR__ . '/states/empty_state_guided.php';
  ?>
<?php endif; ?>

<?php if ($showError): ?>
  <section class="global-state error" role="alert">
    <span class="state-dot" aria-hidden="true"></span>
    <div>
      <strong>Erreur de chargement</strong>
      <div class="muted">Vérifiez la connexion puis réessayez l’action principale.</div>
    </div>
  </section>
<?php endif; ?>
