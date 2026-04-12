<?php
$showLoading = (bool) ($showLoading ?? false);
$showEmpty = (bool) ($showEmpty ?? false);
$showError = (bool) ($showError ?? false);
?>

<?php if ($showLoading): ?>
  <?php require __DIR__ . '/loading_skeleton_card.php'; ?>
<?php endif; ?>

<?php if ($showEmpty): ?>
  <?php require __DIR__ . '/empty_state_guided.php'; ?>
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
