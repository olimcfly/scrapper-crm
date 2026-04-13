<?php
$dateProchaineAction = $prospect['date_prochaine_action'] ?? null;
$isActionOverdue = is_string($dateProchaineAction) && $dateProchaineAction !== '' && $dateProchaineAction < date('Y-m-d');
$pipelineHeat = (string) ($iaSuggestion['heat'] ?? '❄️ froid');
?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1><?= htmlspecialchars((string) ($prospect['full_name'] ?? 'Prospect')) ?></h1>
      <p class="subtitle">Suivi intelligent et actions guidées</p>
    </div>

    <!-- GLOBAL STATES -->
    <?php if (!empty($successMessage)): ?>
      <div class="global-state success">
        <span class="state-dot"></span>
        <p><?= htmlspecialchars((string) $successMessage) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($warningMessage)): ?>
      <div class="global-state error">
        <span class="state-dot"></span>
        <p><?= htmlspecialchars((string) $warningMessage) ?></p>
      </div>
    <?php endif; ?>

    <!-- GRID -->
    <div class="grid">

      <!-- INFOS -->
      <div class="card">
        <div class="card-header">
          <h3>Informations</h3>
        </div>

        <div class="stack-sm">
          <p><strong>Activité :</strong> <span class="muted"><?= htmlspecialchars((string) ($prospect['activity'] ?? '')) ?></span></p>
          <p><strong>Ville :</strong> <span class="muted"><?= htmlspecialchars((string) ($prospect['city'] ?? '')) ?></span></p>
          <p><strong>Pipeline :</strong> <?= htmlspecialchars((string) ($pipeline['stage_name'] ?? 'Nouveau')) ?></p>
          <p><strong>Température :</strong> <?= htmlspecialchars($pipelineHeat) ?></p>
          <p><strong>Dernière action :</strong> <?= htmlspecialchars((string) ($pipeline['last_action'] ?? '—')) ?></p>
          <p><strong>Prochaine action :</strong> <?= htmlspecialchars((string) ($pipeline['next_action'] ?? '—')) ?></p>
        </div>
      </div>

      <!-- ACTIONS -->
      <div class="card">
        <div class="card-header">
          <h3>Actions rapides</h3>
        </div>

        <div class="stack">

          <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/messages">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
            <input type="hidden" name="type" value="dm">
            <input type="hidden" name="direction" value="sent">
            <input type="hidden" name="content" value="<?= htmlspecialchars((string) ($iaSuggestion['message_suggestion'] ?? '')) ?>">
            <button class="btn btn-primary" type="submit">Envoyer message</button>
          </form>

          <a class="btn btn-secondary" href="#add-note">Ajouter note</a>
          <a class="btn btn-secondary" href="/pipeline">Changer étape</a>

        </div>
      </div>

      <!-- IA -->
      <div class="card">
        <div class="card-header">
          <h3>Suggestion IA</h3>
        </div>

        <?php if ($isActionOverdue): ?>
          <div class="badge warning">Action en retard</div>
        <?php endif; ?>

        <div class="stack-sm">
          <p><strong>Next action :</strong> <?= htmlspecialchars((string) ($iaSuggestion['next_action'] ?? '')) ?></p>

          <div class="card small">
            <p><?= nl2br(htmlspecialchars((string) ($iaSuggestion['message_suggestion'] ?? ''))) ?></p>
          </div>

          <details>
            <summary>Voir le prompt IA</summary>
            <pre><?= htmlspecialchars((string) ($iaSuggestion['prompt'] ?? '')) ?></pre>
          </details>

          <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/suggest-next-action">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
            <button class="btn btn-secondary" type="submit">Suggérer nouvelle action</button>
          </form>
        </div>
      </div>

      <!-- MESSAGES -->
      <div class="card">
        <div class="card-header">
          <h3>Historique messages</h3>
        </div>

        <?php if (($messagesTableAvailable ?? true) === false): ?>

          <div class="empty-state">
            <p class="muted">Messagerie non initialisée.</p>
          </div>

        <?php else: ?>

          <form method="post" action="/prospects/<?= (int) $prospect['id'] ?>/messages" class="stack-sm">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

            <div class="row">
              <select name="type" class="input">
                <option value="dm">DM</option>
                <option value="reply">Reply</option>
                <option value="note">Note</option>
              </select>

              <select name="direction" class="input">
                <option value="sent">Envoyé</option>
                <option value="received">Reçu</option>
              </select>
            </div>

            <textarea name="content" class="input" placeholder="Ajouter un message"></textarea>

            <button class="btn btn-primary" type="submit">Enregistrer</button>
          </form>

          <?php if (($messages ?? []) === []): ?>
            <p class="muted">Aucun message</p>
          <?php endif; ?>

          <div class="stack-sm">
            <?php foreach (($messages ?? []) as $message): ?>
              <article class="card small">
                <p class="muted">
                  <?= htmlspecialchars((string) ($message['created_at'] ?? '')) ?>
                  · <?= htmlspecialchars((string) ($message['type'] ?? 'note')) ?>
                </p>
                <p><?= nl2br(htmlspecialchars((string) ($message['content'] ?? ''))) ?></p>
              </article>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>
      </div>

      <!-- NOTES -->
      <div class="card" id="add-note">
        <div class="card-header">
          <h3>Ajouter note</h3>
        </div>

        <form method="post" action="/prospects/<?= (int)$prospect['id'] ?>/notes" class="stack-sm">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
          <textarea name="content" class="input" required></textarea>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>

    </div>

  </div>
</div>