<div class="card">
  <h2>Liste prospects</h2>
  <a class="btn" href="/prospects/create">Créer un prospect</a>
  <a class="btn secondary" href="/prospects/import" style="margin-left:8px;">Importer CSV</a>
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
</div>
