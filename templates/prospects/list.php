<div class="card">
  <h2>Liste prospects</h2>
  <a class="btn" href="/prospects/create">Créer un prospect</a>
</div>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<div class="card">
  <form method="get" action="/prospects">
    <div class="row">
      <div>
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" placeholder="Nom, activité, ville, email..." value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">
      </div>
      <div>
        <label for="status_id">Statut</label>
        <select id="status_id" name="status_id">
          <option value="0">Tous les statuts</option>
          <?php foreach (($statuses ?? []) as $status): ?>
            <option value="<?= (int) $status['id'] ?>" <?= ((int) ($filters['status_id'] ?? 0) === (int) $status['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $status['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="source_id">Source</label>
        <select id="source_id" name="source_id">
          <option value="0">Toutes les sources</option>
          <?php foreach (($sources ?? []) as $source): ?>
            <option value="<?= (int) $source['id'] ?>" <?= ((int) ($filters['source_id'] ?? 0) === (int) $source['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $source['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <p>
      <button class="btn" type="submit">Appliquer</button>
      <a class="btn secondary" href="/prospects">Réinitialiser</a>
    </p>
  </form>
</div>

<div class="card">
  <p class="muted">
    <?= (int) ($pagination['total'] ?? 0) ?> prospect(s) · page <?= (int) ($pagination['page'] ?? 1) ?> / <?= (int) ($pagination['total_pages'] ?? 1) ?>
  </p>

  <table>
    <thead>
    <tr><th>Nom</th><th>Statut</th><th>Source</th><th>Activité</th><th>Ville</th><th>Email</th><th>Score</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if (($prospects ?? []) === []): ?>
      <tr><td colspan="8" class="muted">Aucun prospect trouvé avec ces filtres.</td></tr>
    <?php endif; ?>
    <?php foreach (($prospects ?? []) as $p): ?>
      <tr>
        <td><?= htmlspecialchars((string) ($p['full_name'] ?? (($p['first_name'] ?? '').' '.($p['last_name'] ?? '')))) ?></td>
        <td><?= htmlspecialchars((string) ($p['status_name'] ?? '—')) ?></td>
        <td><?= htmlspecialchars((string) ($p['source_name'] ?? '—')) ?></td>
        <td><?= htmlspecialchars((string) ($p['activity'] ?? '')) ?></td>
        <td><?= htmlspecialchars((string) ($p['city'] ?? '')) ?></td>
        <td><?= htmlspecialchars((string) ($p['professional_email'] ?? '')) ?></td>
        <td><?= (int) ($p['score'] ?? 0) ?></td>
        <td><a class="btn secondary" href="/prospects/<?= (int) $p['id'] ?>">Voir</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php
    $currentPage = (int) ($pagination['page'] ?? 1);
    $totalPages = (int) ($pagination['total_pages'] ?? 1);
    $query = [
      'q' => (string) ($filters['q'] ?? ''),
      'status_id' => (string) ((int) ($filters['status_id'] ?? 0)),
      'source_id' => (string) ((int) ($filters['source_id'] ?? 0)),
    ];
  ?>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php if ($currentPage > 1): ?>
        <?php $query['page'] = (string) ($currentPage - 1); ?>
        <a class="btn secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">← Précédent</a>
      <?php endif; ?>
      <?php if ($currentPage < $totalPages): ?>
        <?php $query['page'] = (string) ($currentPage + 1); ?>
        <a class="btn secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">Suivant →</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
