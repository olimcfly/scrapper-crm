<div class="card">
  <h2>Liste prospects</h2>
  <a class="btn" href="/prospects/create">Créer un prospect</a>
</div>

<div class="card">
  <form method="get" action="/prospects">
    <div class="row">
      <div>
        <label>Recherche</label>
        <input type="text" name="q" value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>" placeholder="Nom, activité, ville, email...">
      </div>
      <div>
        <label>Statut</label>
        <select name="status_id">
          <option value="0">Tous</option>
          <?php foreach (($statuses ?? []) as $status): ?>
            <option value="<?= (int) $status['id'] ?>" <?= ((int) ($filters['status_id'] ?? 0) === (int) $status['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $status['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Source</label>
        <select name="source_id">
          <option value="0">Toutes</option>
          <?php foreach (($sources ?? []) as $source): ?>
            <option value="<?= (int) $source['id'] ?>" <?= ((int) ($filters['source_id'] ?? 0) === (int) $source['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $source['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <p>
      <button type="submit" class="btn">Filtrer</button>
      <a href="/prospects" class="btn secondary">Réinitialiser</a>
    </p>
  </form>
</div>

<div class="card">
  <table>
    <thead>
    <tr><th>Nom</th><th>Activité</th><th>Ville</th><th>Email</th><th>Score</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach (($prospects ?? []) as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['full_name'] ?? ($p['first_name'].' '.$p['last_name'])) ?></td>
        <td><?= htmlspecialchars($p['activity'] ?? '') ?></td>
        <td><?= htmlspecialchars($p['city'] ?? '') ?></td>
        <td><?= htmlspecialchars($p['professional_email'] ?? '') ?></td>
        <td><?= (int)($p['score'] ?? 0) ?></td>
        <td><a class="btn secondary" href="/prospects/<?= (int)$p['id'] ?>">Voir</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php $page = (int) ($pagination['page'] ?? 1); ?>
  <?php $totalPages = (int) ($pagination['total_pages'] ?? 1); ?>
  <?php if ($totalPages > 1): ?>
    <p style="margin-top:14px;">
      <?php if ($page > 1): ?>
        <a class="btn secondary" href="/prospects?<?= http_build_query([
          'q' => (string) ($filters['q'] ?? ''),
          'status_id' => (int) ($filters['status_id'] ?? 0),
          'source_id' => (int) ($filters['source_id'] ?? 0),
          'page' => $page - 1,
        ]) ?>">Précédent</a>
      <?php endif; ?>
      <span style="margin:0 10px;">Page <?= $page ?> / <?= $totalPages ?></span>
      <?php if ($page < $totalPages): ?>
        <a class="btn secondary" href="/prospects?<?= http_build_query([
          'q' => (string) ($filters['q'] ?? ''),
          'status_id' => (int) ($filters['status_id'] ?? 0),
          'source_id' => (int) ($filters['source_id'] ?? 0),
          'page' => $page + 1,
        ]) ?>">Suivant</a>
      <?php endif; ?>
    </p>
  <?php endif; ?>
</div>
