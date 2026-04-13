<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Import de prospects</h1>
      <p class="subtitle">
        Importez vos prospects via un fichier CSV et structurez vos données automatiquement
      </p>
    </div>

    <!-- FORM -->
    <div class="card">

      <div class="card-header">
        <h3>Uploader un fichier CSV</h3>
      </div>

      <!-- ERRORS -->
      <?php if (($errors ?? []) !== []): ?>
        <div class="text-error">
          <?php foreach ($errors as $error): ?>
            <p>• <?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post"
            action="/prospects/import/upload"
            enctype="multipart/form-data"
            class="stack">

        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <div class="form-group">
          <label for="csv_file">Fichier CSV</label>
          <input
            id="csv_file"
            type="file"
            name="csv_file"
            accept=".csv,text/csv"
            required
            class="input"
          >
        </div>

        <div class="muted">
          Format attendu : 1 ligne d’en-têtes + lignes de données
        </div>

        <button class="btn btn-primary" type="submit">
          Continuer vers le mapping
        </button>

      </form>

    </div>

  </div>
</div>