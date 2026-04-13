<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Import CSV terminé</h1>
      <p class="subtitle">Résumé du traitement de votre fichier</p>
    </div>

    <!-- REPORT -->
    <div class="card">

      <div class="card-header">
        <h3>Résultat de l’import</h3>
      </div>

      <p class="muted">
        Fichier : <strong><?= htmlspecialchars($fileName ?? 'import.csv') ?></strong>
      </p>

      <div class="grid">

        <div class="card small">
          <p class="muted">Lignes analysées</p>
          <h3><?= (int) ($totalRows ?? 0) ?></h3>
        </div>

        <div class="card small">
          <p class="muted">Succès</p>
          <h3><?= (int) ($report['success_count'] ?? 0) ?></h3>
        </div>

        <div class="card small">
          <p class="muted">Erreurs</p>
          <h3><?= (int) ($report['error_count'] ?? 0) ?></h3>
        </div>

      </div>

      <div class="row" style="margin-top:16px;">
        <a class="btn btn-primary" href="/prospects">
          Voir les prospects
        </a>

        <a class="btn btn-secondary" href="/prospects/import">
          Importer un autre CSV
        </a>
      </div>

    </div>

    <!-- ERRORS -->
    <?php if (($report['errors'] ?? []) !== []): ?>

      <div class="card">

        <div class="card-header">
          <h3>Détails des erreurs</h3>
        </div>

        <div class="stack-sm text-error">
          <?php foreach (($report['errors'] ?? []) as $error): ?>
            <p>• <?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>

      </div>

    <?php endif; ?>

  </div>
</div>