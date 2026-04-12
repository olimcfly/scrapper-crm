<?php
$dateProchaineAction = $prospect['date_prochaine_action'] ?? null;
$isActionOverdue = is_string($dateProchaineAction) && $dateProchaineAction !== '' && $dateProchaineAction < date('Y-m-d');
$pipelineHeat = (string) ($iaSuggestion['heat'] ?? '❄️ froid');
?>

<?php if (!empty($successMessage)): ?>
  <div class="global-state loading"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $successMessage) ?></p></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="global-state error"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div>
<?php endif; ?>

<div class="card">
  <h2 style="margin:0 0 6px;"><?= htmlspecialchars((string) ($prospect['full_name'] ?? '')) ?></h2>
  <p class="muted" style="margin:0 0 8px;"><?= htmlspecialchars((string) ($prospect['activity'] ?? 'Plateforme inconnue')) ?> · <?= htmlspecialchars((string) ($prospect['city'] ?? '')) ?></p>
  <p style="margin:0 0 8px;">Pipeline: <strong><?= htmlspecialchars((string) ($pipeline['stage_name'] ?? 'Nouveau')) ?></strong> · Température: <strong><?= htmlspecialchars($pipelineHeat) ?></strong></p>
  <p style="margin:0;">Dernière action: <?= htmlspecialchars((string) ($pipeline['last_action'] ?? '—')) ?> | Prochaine action: <strong><?= htmlspecialchars((string) ($pipeline['next_action'] ?? '—')) ?></strong></p>
</div>

<div class="card">
  <h3 style="margin-top:0;">Actions guidées</h3>
  <div class="row">
    <div>
      <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/messages">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <input type="hidden" name="type" value="dm">
        <input type="hidden" name="direction" value="sent">
        <input type="hidden" name="content" value="<?= htmlspecialchars((string) ($iaSuggestion['message_suggestion'] ?? '')) ?>">
        <button class="btn" type="submit">Envoyer message</button>
      </form>
    </div>
    <div><a class="btn secondary" href="#add-note">Ajouter note</a></div>
    <div><a class="btn" href="/pipeline">Changer étape</a></div>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Suggestion IA de conversion</h3>
  <?php if ($isActionOverdue): ?>
    <p class="muted">⚠️ Action en retard détectée.</p>
  <?php endif; ?>
  <p><strong>Next action:</strong> <?= htmlspecialchars((string) ($iaSuggestion['next_action'] ?? '')) ?></p>
  <p><strong>Message suggestion:</strong><br><?= nl2br(htmlspecialchars((string) ($iaSuggestion['message_suggestion'] ?? ''))) ?></p>
  <details>
    <summary>Voir le prompt IA</summary>
    <pre style="white-space:pre-wrap;"><?= htmlspecialchars((string) ($iaSuggestion['prompt'] ?? '')) ?></pre>
  </details>
  <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/suggest-next-action" style="margin-top:10px;">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
    <button class="btn" type="submit">Suggérer prochaine action</button>
  </form>
</div>

<div class="card">
  <h3 style="margin-top:0;">Historique messages</h3>
  <?php if (($messagesTableAvailable ?? true) === false): ?>
    <p class="muted">Messagerie non initialisée pour cet environnement. Exécutez la migration pour activer l’historique.</p>
  <?php else: ?>
    <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/messages">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
      <div class="row">
        <div>
          <select name="type">
            <option value="dm">DM</option>
            <option value="reply">Reply</option>
            <option value="note">Note</option>
          </select>
        </div>
        <div>
          <select name="direction">
            <option value="sent">Envoyé</option>
            <option value="received">Reçu</option>
          </select>
        </div>
      </div>
      <textarea name="content" placeholder="Ajouter un message ou une note" required></textarea>
      <p><button class="btn" type="submit">Enregistrer message</button></p>
    </form>

    <?php if (($messages ?? []) === []): ?>
      <p class="muted">Aucun message enregistré pour ce prospect.</p>
    <?php endif; ?>
    <?php foreach (($messages ?? []) as $message): ?>
      <article style="border-top:1px solid #e2e8f0;padding-top:8px;margin-top:8px;">
        <p class="muted" style="margin:0;"><?= htmlspecialchars((string) ($message['created_at'] ?? '')) ?> · <?= htmlspecialchars((string) ($message['type'] ?? 'note')) ?> · <?= htmlspecialchars((string) ($message['direction'] ?? 'sent')) ?></p>
        <p style="margin:4px 0 0;"><?= nl2br(htmlspecialchars((string) ($message['content'] ?? ''))) ?></p>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<div class="card" id="add-note">
  <h3 style="margin-top:0;">Ajouter note</h3>
  <form method="post" action="/prospects/<?= (int)$prospect['id'] ?>/notes">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
    <textarea name="content" required></textarea>
    <p><button class="btn" type="submit">Ajouter note</button></p>
  </form>
</div>
