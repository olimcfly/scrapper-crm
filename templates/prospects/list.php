<div class="card">
  <h2>Prospects</h2>
  <p class="muted">Hub prospect-first : liste consultable en mobile (cartes) et desktop (table).</p>
  <a class="btn" href="/prospects/create">Créer un prospect</a>
  <a class="btn secondary" href="/prospects/import" style="margin-left:8px;">Importer CSV</a>
</div>

<?php if (!empty($successMessage)): ?>
  <div class="global-state loading"><span class="state-dot" aria-hidden="true"></span><div><?= htmlspecialchars((string) $successMessage) ?></div></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="global-state error"><span class="state-dot" aria-hidden="true"></span><div><?= htmlspecialchars((string) $warningMessage) ?></div></div>
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

  <?php if (empty($prospects)): ?>
    <?php
      $title = 'Aucun prospect trouvé';
      $message = 'Ajustez les filtres ou ajoutez un nouveau prospect pour alimenter la stratégie.';
      $ctaHref = '/prospects/create';
      $ctaLabel = 'Créer un prospect';
      require __DIR__ . '/../components/empty_state_guided.php';
    ?>
  <?php else: ?>
    <div class="prospect-mobile-list" style="display:grid;gap:12px;">
      <?php foreach ($prospects as $p): ?>
        <article class="kpi-card">
          <strong><?= htmlspecialchars((string) ($p['full_name'] ?? (($p['first_name'] ?? '').' '.($p['last_name'] ?? '')))) ?></strong>
          <p class="muted" style="margin:6px 0;">Statut: <?= htmlspecialchars((string) ($p['status_name'] ?? '—')) ?> · Score: <?= (int) ($p['score'] ?? 0) ?></p>
          <a class="btn secondary compact" href="/prospects/<?= (int) $p['id'] ?>">Ouvrir</a>
        </article>
      <?php endforeach; ?>
    </div>

    <table class="prospect-table" style="display:none;">
      <thead>
      <tr><th>Nom</th><th>Statut</th><th>Source</th><th>Activité</th><th>Ville</th><th>Email</th><th>Score</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($prospects as $p): ?>
        <tr>
          <td><?= htmlspecialchars((string) ($p['full_name'] ?? (($p['first_name'] ?? '').' '.($p['last_name'] ?? '')))) ?></td>
          <td><?= htmlspecialchars((string) ($p['status_name'] ?? '—')) ?></td>
          <td><?= htmlspecialchars((string) ($p['source_name'] ?? '—')) ?></td>
          <td><?= htmlspecialchars((string) ($p['activity'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($p['city'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($p['professional_email'] ?? '')) ?></td>
          <td><?= (int) ($p['score'] ?? 0) ?></td>
          <td><a class="btn secondary compact" href="/prospects/<?= (int) $p['id'] ?>">Voir</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <style>@media (min-width:900px){.prospect-mobile-list{display:none!important}.prospect-table{display:table!important}}</style>
  <?php endif; ?>

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
