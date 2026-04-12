<div class="card">
  <h2><?= htmlspecialchars($prospect['full_name'] ?? '') ?></h2>
  <p><?= htmlspecialchars($prospect['activity'] ?? '') ?> · <?= htmlspecialchars($prospect['city'] ?? '') ?></p>
  <p>Email: <?= htmlspecialchars($prospect['professional_email'] ?? '') ?> | Tél: <?= htmlspecialchars($prospect['professional_phone'] ?? '') ?></p>
  <p>
    <a class="btn" href="/prospects/<?= (int)$prospect['id'] ?>/edit">Modifier</a>
    <form style="display:inline" method="post" action="/prospects/<?= (int)$prospect['id'] ?>/delete" onsubmit="return confirm('Supprimer ce prospect ?')">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
      <button class="btn danger" type="submit">Supprimer</button>
    </form>
  </p>
</div>

<div class="card">
  <h3>Changer le statut</h3>
  <form method="post" action="/prospects/<?= (int)$prospect['id'] ?>/status">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
    <div class="row">
      <div>
        <select name="status_id">
          <?php foreach (($statuses ?? []) as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= ((int)($prospect['status_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><button class="btn" type="submit">Mettre à jour</button></div>
    </div>
  </form>
</div>

<div class="card">
  <h3>Ajouter une note</h3>
  <form method="post" action="/prospects/<?= (int)$prospect['id'] ?>/notes">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
    <textarea name="content" required></textarea>
    <p><button class="btn" type="submit">Ajouter note</button></p>
  </form>
</div>

<div class="card">
  <h3>Historique des notes</h3>
  <?php foreach (($notes ?? []) as $n): ?>
    <p><strong><?= htmlspecialchars($n['created_at'] ?? '') ?></strong><br><?= nl2br(htmlspecialchars($n['content'] ?? '')) ?></p><hr>
  <?php endforeach; ?>
</div>
