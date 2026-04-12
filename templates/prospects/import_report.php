<div class="card">
  <h2>Rapport d'import CSV</h2>
  <p>Fichier traité: <strong><?= htmlspecialchars($fileName ?? 'import.csv') ?></strong></p>

  <ul>
    <li>Lignes analysées: <strong><?= (int) ($totalRows ?? 0) ?></strong></li>
    <li>Succès: <strong><?= (int) (($report['success_count'] ?? 0)) ?></strong></li>
    <li>Erreurs: <strong><?= (int) (($report['error_count'] ?? 0)) ?></strong></li>
  </ul>

  <a class="btn" href="/prospects">Retour à la liste</a>
  <a class="btn secondary" href="/prospects/import">Importer un autre CSV</a>
</div>

<?php if (($report['errors'] ?? []) !== []): ?>
  <div class="card">
    <h3>Détails des erreurs</h3>
    <div class="errors">
      <ul>
        <?php foreach (($report['errors'] ?? []) as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>
