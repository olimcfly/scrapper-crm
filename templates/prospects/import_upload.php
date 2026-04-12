<div class="card">
  <h2>Import CSV prospects</h2>
  <p>Chargez un fichier CSV contenant vos prospects, puis mappez chaque colonne.</p>

  <?php if (($errors ?? []) !== []): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/prospects/import/upload" enctype="multipart/form-data">
    <label for="csv_file">Fichier CSV</label>
    <input id="csv_file" type="file" name="csv_file" accept=".csv,text/csv" required>
    <p style="color:#6b7280">Format attendu: 1 ligne d'en-têtes + lignes de données.</p>
    <button class="btn" type="submit">Continuer vers le mapping</button>
  </form>
</div>
