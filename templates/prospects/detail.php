<div class="card">
  <h2><?= htmlspecialchars((string) ($prospect['full_name'] ?? '')) ?></h2>
  <p><?= htmlspecialchars((string) ($prospect['activity'] ?? '')) ?> · <?= htmlspecialchars((string) ($prospect['city'] ?? '')) ?></p>
  <p>Statut: <strong><?= htmlspecialchars((string) ($prospect['status_name'] ?? '—')) ?></strong> · Source: <strong><?= htmlspecialchars((string) ($prospect['source_name'] ?? '—')) ?></strong></p>
  <p>Email: <?= htmlspecialchars((string) ($prospect['professional_email'] ?? '')) ?> | Tél: <?= htmlspecialchars((string) ($prospect['professional_phone'] ?? '')) ?></p>
  <p>
    <a class="btn" href="/prospects/<?= (int) $prospect['id'] ?>/edit">Modifier</a>
    <form style="display:inline" method="post" action="/prospects/<?= (int) $prospect['id'] ?>/delete" onsubmit="return confirm('Supprimer ce prospect ?')">
      <button class="btn danger" type="submit">Supprimer</button>
    </form>
  </p>
</div>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<div class="card">
  <h3>Changer le statut</h3>
  <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/status">
    <div class="row">
      <div>
        <select name="status_id">
          <?php foreach (($statuses ?? []) as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= ((int) ($prospect['status_id'] ?? 0) === (int) $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars((string) $s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><button class="btn" type="submit">Mettre à jour</button></div>
    </div>
  </form>
</div>

<div class="card">
  <h3>Ajouter une note</h3>
  <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/notes">
    <textarea name="content" required placeholder="Ex: Appelé le 12/04, relance prévue mardi."></textarea>
    <p><button class="btn" type="submit">Ajouter note</button></p>
  </form>
</div>

<div class="card">
  <h3>Timeline métier</h3>
  <?php if (($timeline ?? []) === []): ?>
    <p class="muted">Aucun événement pour le moment.</p>
  <?php endif; ?>
  <?php foreach (($timeline ?? []) as $event): ?>
    <div class="timeline-item">
      <p class="muted"><?= htmlspecialchars((string) ($event['created_at'] ?? '')) ?> · <?= htmlspecialchars((string) ($event['event_type'] ?? 'event')) ?></p>
      <p><?= nl2br(htmlspecialchars((string) ($event['details'] ?? ''))) ?></p>
    </div>
  <?php endforeach; ?>
</div>
