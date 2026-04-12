<div class="card">
  <h2>Mapping des colonnes</h2>
  <p>Fichier: <strong><?= htmlspecialchars($fileName ?? 'import.csv') ?></strong></p>
  <p>Associez chaque champ CRM à une colonne CSV.</p>

  <?php if (($errors ?? []) !== []): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/prospects/import/process">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
    <div class="row">
      <?php foreach (($fieldLabels ?? []) as $field => $label): ?>
        <div>
          <label for="map_<?= htmlspecialchars($field) ?>"><?= htmlspecialchars($label) ?></label>
          <select id="map_<?= htmlspecialchars($field) ?>" name="map_<?= htmlspecialchars($field) ?>">
            <option value="">-- Ignorer --</option>
            <?php foreach (($headers ?? []) as $header): ?>
              <?php $selected = (($selectedMapping[$field] ?? '') === $header) ? 'selected' : ''; ?>
              <option value="<?= htmlspecialchars($header) ?>" <?= $selected ?>><?= htmlspecialchars($header) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endforeach; ?>
    </div>

    <p style="color:#6b7280">* Prénom et Nom sont obligatoires pour importer une ligne.</p>
    <button class="btn" type="submit">Importer en base</button>
    <a class="btn secondary" href="/prospects/import">Annuler</a>
  </form>
</div>

<div class="card">
  <h3>Aperçu des 5 premières lignes</h3>
  <table>
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
