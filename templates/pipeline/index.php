<?php
$stageCount = 0;
$stages = is_array($stages ?? null) ? $stages : [];
$grouped = is_array($grouped ?? null) ? $grouped : [];
?>

<div class="page pipeline-page">
  <div class="container">

    <div class="page-header">
      <h1>Pipeline orienté action</h1>
      <p class="subtitle">Change d’étape en 1 tap, relance instantanée et coaching IA sur chaque carte.</p>
    </div>

    <?php if (!empty($successMessage)): ?>
      <div class="global-state success"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $successMessage) ?></p></div>
    <?php endif; ?>

    <?php if (!empty($warningMessage)): ?>
      <div class="global-state error"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div>
    <?php endif; ?>

    <div class="card">
      <?php if (($pipelineStagesAvailable ?? true) === false): ?>
        <?php
          $emptyTitle = 'Pipeline non initialisé';
          $emptyDescription = 'Exécute les migrations SQL pour activer les étapes et débloquer les actions IA.';
          $emptyCtaHref = '/settings';
          $emptyCtaLabel = 'Voir la configuration';
          require __DIR__ . '/../components/states/empty_state_guided.php';
        ?>
      <?php else: ?>

        <div class="pipeline-columns">
          <?php foreach ($stages as $index => $stage): ?>
            <?php
              $stageId = (int) ($stage['id'] ?? 0);
              $cards = $grouped[$stageId] ?? [];
              $stageCount += count($cards);
            ?>
            <section class="pipeline-stage">
              <header class="pipeline-stage-header">
                <strong><?= htmlspecialchars((string) ($stage['name'] ?? 'Étape')) ?></strong>
                <span class="badge"><?= count($cards) ?></span>
              </header>

              <?php if ($cards === []): ?>
                <div class="empty-state small">
                  <p class="muted">Aucun prospect ici.</p>
                  <a class="text-link" href="/prospects/create">Ajouter un prospect</a>
                </div>
              <?php endif; ?>

              <div class="stack">
                <?php foreach ($cards as $prospect): ?>
                  <?php
                    $currentStageId = (int) ($prospect['stage_id'] ?? 0);
                    $nextStage = null;
                    foreach ($stages as $stageOption) {
                      if ((int) ($stageOption['id'] ?? 0) === $currentStageId) {
                        continue;
                      }
                      if ($nextStage === null && (int) ($stageOption['position'] ?? 0) > (int) ($stage['position'] ?? 0)) {
                        $nextStage = $stageOption;
                      }
                    }
                  ?>
                  <article class="pipeline-card">
                    <a href="/prospects/<?= (int) $prospect['prospect_id'] ?>" class="pipeline-card-link">
                      <strong><?= htmlspecialchars((string) ($prospect['full_name'] ?? 'Prospect')) ?></strong>
                      <p class="muted"><?= htmlspecialchars((string) ($prospect['activity'] ?? 'Plateforme non définie')) ?></p>
                      <small class="muted">Dernière action : <?= htmlspecialchars((string) ($prospect['last_action'] ?? '—')) ?></small>
                    </a>

                    <div class="pipeline-card-actions">
                      <?php if (is_array($nextStage)): ?>
                        <form method="post" action="/pipeline/<?= (int) $prospect['prospect_id'] ?>/move">
                          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                          <input type="hidden" name="stage_id" value="<?= (int) $nextStage['id'] ?>">
                          <button class="btn btn-primary btn-compact" type="submit">Changer étape</button>
                        </form>
                      <?php endif; ?>

                      <a class="btn btn-secondary btn-compact" href="/messages-ia?type=relance">Relancer</a>
                    </div>

                    <div class="pipeline-card-ai ai-suggestion-block">
                      <p class="ai-suggestion-title">Suggestion IA</p>
                      <p>Je te suggère une relance courte avec bénéfice concret + question fermée pour obtenir une réponse rapide.</p>
                    </div>

                    <form method="post" action="/pipeline/<?= (int) $prospect['prospect_id'] ?>/move" class="pipeline-form-row">
                      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                      <select name="stage_id" class="input">
                        <?php foreach ($stages as $option): ?>
                          <option value="<?= (int) $option['id'] ?>" <?= ((int) $option['id'] === $currentStageId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $option['name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <button class="btn btn-secondary btn-compact" type="submit">Valider</button>
                    </form>
                  </article>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="card">
      <p class="muted"><?= (int) $stageCount ?> prospects actifs dans ton pipeline</p>
    </div>

  </div>
</div>
