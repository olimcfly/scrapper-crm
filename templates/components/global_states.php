<?php
$showLoading = (bool) ($showLoading ?? false);
$showEmpty = (bool) ($showEmpty ?? false);
$showError = (bool) ($showError ?? false);
?>

<?php if ($showLoading): ?>
  <section class="global-state loading" role="status" aria-live="polite">
    <span class="state-dot" aria-hidden="true"></span>
    <div>
      <strong>Chargement en cours</strong>
      <div class="muted">Les données principales arrivent…</div>
    </div>
  </section>
<?php endif; ?>

<?php if ($showEmpty): ?>
  <section class="global-state empty">
    <span class="state-dot" aria-hidden="true"></span>
    <div>
      <strong>Aucune donnée disponible</strong>
      <div class="muted">Commencez par ajouter un prospect pour activer le flux prospect-first.</div>
    </div>
  </section>
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
