<?php
$showLoading = (bool) ($showLoading ?? false);
$showEmpty = (bool) ($showEmpty ?? false);
$showError = (bool) ($showError ?? false);
?>

<?php if ($showLoading): ?>
  <?php include __DIR__ . '/LoadingSkeletonCard.php'; ?>
<?php endif; ?>

<?php if ($showEmpty): ?>
  <?php include __DIR__ . '/EmptyStateGuided.php'; ?>
<?php endif; ?>

<?php if ($showError): ?>
  <section class="global-state error" role="alert">
    <span class="state-dot" aria-hidden="true"></span>
    <div>
      <strong>Erreur de chargement</strong>
      <div class="muted">Vérifiez la connexion puis relancez l’action principale.</div>
    </div>
  </section>
<?php endif; ?>
