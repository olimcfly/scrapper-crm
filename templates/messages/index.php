<?php

function safe_array($value) {
  return is_array($value) ? $value : [];
}

function safe_string($value) {
  return htmlspecialchars((string) ($value ?? ''));
}

$analysisData = safe_array($analysis ?? null);
$historyData = safe_array($history ?? null);

$messageTypeValue = (string) ($selectedType ?? 'dm');
$channelValue = (string) ($selectedChannel ?? 'whatsapp');
?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Messages IA</h1>
      <p class="subtitle">Génération intelligente de messages à partir de vos analyses</p>
    </div>

    <!-- STATES -->
    <?php if (!empty($successMessage)): ?>
      <div class="global-state success">
        <span class="state-dot"></span>
        <p><?= safe_string($successMessage) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($warningMessage)): ?>
      <div class="global-state warning">
        <span class="state-dot"></span>
        <p><?= safe_string($warningMessage) ?></p>
      </div>
    <?php endif; ?>

    <?php if ($analysisData === [] || (int) ($analysisId ?? 0) <= 0): ?>

      <div class="card">
        <div class="empty-state">
          <p>Aucune analyse disponible</p>
          <a class="btn btn-primary" href="/strategie">Créer une analyse</a>
        </div>
      </div>

    <?php else: ?>

      <div class="grid">

        <!-- LEFT -->
        <div class="stack">

          <!-- ANALYSE -->
          <div class="card">
            <div class="card-header">
              <h3>Analyse source #<?= (int) $analysisId ?></h3>
            </div>

            <div class="stack-sm">

              <div class="card small">
                <strong>Résumé</strong>
                <p><?= nl2br(safe_string($analysisData['summary'] ?? '')) ?></p>
              </div>

              <div class="card small">
                <strong>Niveau de conscience</strong>
                <p><?= safe_string($analysisData['awareness_level'] ?? 'N/A') ?></p>
              </div>

              <?php if (!empty($draftText)): ?>
                <div class="card small">
                  <strong>Brouillon actif</strong>
                  <p><?= nl2br(safe_string($draftText)) ?></p>
                </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- HISTORY -->
          <div class="card">
            <div class="card-header">
              <h3>Historique des brouillons</h3>
            </div>

            <?php if ($historyData === []): ?>

              <div class="empty-state">
                <p>Aucun message généré</p>
              </div>

            <?php else: ?>

              <div class="stack-sm">

                <?php foreach ($historyData as $item): ?>
                  <article class="card small">

                    <p class="muted">
                      <?= safe_string($item['created_at'] ?? '') ?>
                      · <?= safe_string($item['message_type'] ?? '') ?>
                      · <?= safe_string($item['channel'] ?? '') ?>
                    </p>

                    <p><?= nl2br(safe_string($item['message_text'] ?? '')) ?></p>

                    <div class="row">
                      <a class="btn btn-secondary" href="/messages-ia?draft_id=<?= (int) ($item['id'] ?? 0) ?>">
                        Rouvrir
                      </a>

                      <form method="post" action="/messages-ia/dupliquer">
                        <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">
                        <input type="hidden" name="draft_id" value="<?= (int) ($item['id'] ?? 0) ?>">
                        <button class="btn btn-primary" type="submit">
                          Dupliquer
                        </button>
                      </form>
                    </div>

                  </article>
                <?php endforeach; ?>

              </div>

            <?php endif; ?>

          </div>

        </div>

        <!-- RIGHT -->
        <div>

          <div class="card">
            <div class="card-header">
              <h3>Générer un message</h3>
            </div>

            <form method="post" action="/messages-ia/generer" class="stack">

              <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">

              <div class="form-group">
                <label>Type</label>
                <select name="message_type" class="input">
                  <option value="dm" <?= $messageTypeValue === 'dm' ? 'selected' : '' ?>>DM</option>
                  <option value="relance" <?= $messageTypeValue === 'relance' ? 'selected' : '' ?>>Relance</option>
                  <option value="reponse" <?= $messageTypeValue === 'reponse' ? 'selected' : '' ?>>Réponse</option>
                </select>
              </div>

              <div class="form-group">
                <label>Canal</label>
                <select name="channel" class="input">
                  <option value="whatsapp" <?= $channelValue === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                  <option value="instagram" <?= $channelValue === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                  <option value="linkedin" <?= $channelValue === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                  <option value="email" <?= $channelValue === 'email' ? 'selected' : '' ?>>Email</option>
                  <option value="sms" <?= $channelValue === 'sms' ? 'selected' : '' ?>>SMS</option>
                </select>
              </div>

              <button class="btn btn-primary" type="submit">
                Générer un brouillon
              </button>

            </form>

          </div>

        </div>

      </div>

    <?php endif; ?>

  </div>
</div>