<div class="card">
  <h2>Liste prospects</h2>
  <a class="btn" href="/prospects/create">Créer un prospect</a>
  <a class="btn secondary" href="/prospects/import" style="margin-left:8px;">Importer CSV</a>
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
  <?php
    $filters = $filters ?? ['q' => '', 'sort' => 'date', 'page' => 1, 'limit' => 20];
    $pagination = $pagination ?? ['total' => 0, 'page' => 1, 'limit' => 20, 'total_pages' => 1, 'has_prev' => false, 'has_next' => false];
    $buildQuery = static function (array $overrides = []) use ($filters): string {
        $params = [
            'q' => (string) ($filters['q'] ?? ''),
            'sort' => (string) ($filters['sort'] ?? 'date'),
            'limit' => (int) ($filters['limit'] ?? 20),
            'page' => (int) ($filters['page'] ?? 1),
        ];

        foreach ($overrides as $k => $v) {
            $params[$k] = $v;
        }

        return http_build_query($params);
    };
  ?>

  <form method="get" action="/prospects" style="margin-bottom:14px;">
    <div class="row">
      <div style="flex:2">
        <label for="q">Recherche</label>
        <input id="q" type="text" name="q" placeholder="Nom, activité, ville, email..." value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">
      </div>
      <div>
        <label for="sort">Tri</label>
        <select id="sort" name="sort">
          <option value="date" <?= ($filters['sort'] ?? 'date') === 'date' ? 'selected' : '' ?>>Date création</option>
          <option value="name" <?= ($filters['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Nom</option>
          <option value="score" <?= ($filters['sort'] ?? '') === 'score' ? 'selected' : '' ?>>Score</option>
          <option value="city" <?= ($filters['sort'] ?? '') === 'city' ? 'selected' : '' ?>>Ville</option>
        </select>
      </div>
      <div>
        <label for="limit">Par page</label>
        <select id="limit" name="limit">
          <?php foreach ([10, 20, 50, 100] as $pageLimit): ?>
            <option value="<?= $pageLimit ?>" <?= (int) ($filters['limit'] ?? 20) === $pageLimit ? 'selected' : '' ?>><?= $pageLimit ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div style="margin-top:10px;display:flex;gap:8px;">
      <button class="btn" type="submit">Appliquer</button>
      <a class="btn secondary" href="/prospects">Réinitialiser</a>
    </div>
  </form>

  <p style="margin-top:0;color:#6b7280;">
    Total : <?= (int) ($pagination['total'] ?? 0) ?> prospect(s) ·
    Page <?= (int) ($pagination['page'] ?? 1) ?> / <?= (int) ($pagination['total_pages'] ?? 1) ?>
  </p>

  <table>
    <thead>
    <tr><th>Nom</th><th>Activité</th><th>Ville</th><th>Email</th><th>Score</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if (($prospects ?? []) === []): ?>
      <tr>
        <td colspan="6" style="text-align:center;color:#6b7280;">Aucun prospect trouvé.</td>
      </tr>
    <?php else: ?>
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
    <?php endif; ?>
    </tbody>
  </table>

  <div style="margin-top:12px;display:flex;justify-content:space-between;gap:10px;">
    <div>
      <?php if (($pagination['has_prev'] ?? false) === true): ?>
        <a class="btn secondary" href="/prospects?<?= htmlspecialchars($buildQuery(['page' => (int) $pagination['page'] - 1])) ?>">← Précédent</a>
      <?php endif; ?>
    </div>
    <div>
      <?php if (($pagination['has_next'] ?? false) === true): ?>
        <a class="btn secondary" href="/prospects?<?= htmlspecialchars($buildQuery(['page' => (int) $pagination['page'] + 1])) ?>">Suivant →</a>
      <?php endif; ?>
    </div>
  </div>
</div>
