<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Import CSV</h1>
      <p class="subtitle">Associez les colonnes de votre fichier avec votre CRM</p>
    </div>

    <!-- FORM -->
    <div class="card">

      <div class="card-header">
        <h3>Mapping des colonnes</h3>
      </div>

      <p class="muted">
        Fichier : <strong><?= htmlspecialchars($fileName ?? 'import.csv') ?></strong>
      </p>

      <!-- ERRORS -->
      <?php if (($errors ?? []) !== []): ?>
        <div class="text-error">
          <?php foreach ($errors as $error): ?>
            <p>• <?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="/prospects/import/process" class="stack">

        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <div class="grid">

          <?php foreach (($fieldLabels ?? []) as $field => $label): ?>
            <div class="form-group">
              <label for="map_<?= htmlspecialchars($field) ?>">
                <?= htmlspecialchars($label) ?>
              </label>

              <select class="input"
                      id="map_<?= htmlspecialchars($field) ?>"
                      name="map_<?= htmlspecialchars($field) ?>">

                <option value="">-- Ignorer --</option>

                <?php foreach (($headers ?? []) as $header): ?>
                  <?php $selected = (($selectedMapping[$field] ?? '') === $header) ? 'selected' : ''; ?>
                  <option value="<?= htmlspecialchars($header) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($header) ?>
                  </option>
                <?php endforeach; ?>

              </select>
            </div>
          <?php endforeach; ?>

        </div>

        <div class="muted">
          * Prénom et Nom sont obligatoires pour importer une ligne
        </div>

        <div class="row">
          <button class="btn btn-primary" type="submit">
            Importer en base
          </button>

          <a class="btn btn-secondary" href="/prospects/import">
            Annuler
          </a>
        </div>

      </form>

    </div>

    <!-- PREVIEW -->
    <div class="card">

      <div class="card-header">
        <h3>Aperçu du fichier</h3>
      </div>

      <?php if (($sampleRows ?? []) === []): ?>

        <div class="empty-state">
          <p class="muted">Aucune donnée à afficher</p>
        </div>

      <?php else: ?>

        <div class="table-wrapper">
          <table class="table">

            <thead>
              <tr>
                <?php foreach (($headers ?? []) as $header): ?>
                  <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
              </tr>
            </thead>

            <tbody>
              <?php foreach (($sampleRows ?? []) as $row): ?>
                <tr>
                  <?php foreach ($row as $value): ?>
                    <td><?= htmlspecialchars($value) ?></td>
                  <?php endforeach; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
        </div>

      <?php endif; ?>

    </div>

  </div>
</div>