<?php
$stageCount = 0;
?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Pipeline</h1>
      <p class="subtitle">Suivi intelligent de tes prospects par étape</p>
    </div>

    <!-- GLOBAL STATES -->
    <?php if (!empty($successMessage)): ?>
      <div class="global-state success">
        <span class="state-dot" aria-hidden="true"></span>
        <p><?= htmlspecialchars((string) $successMessage) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($warningMessage)): ?>
      <div class="global-state error">
        <span class="state-dot" aria-hidden="true"></span>
        <p><?= htmlspecialchars((string) $warningMessage) ?></p>
      </div>
    <?php endif; ?>

    <!-- PIPELINE -->
    <div class="card">

      <?php if (($pipelineStagesAvailable ?? true) === false): ?>

        <div class="empty-state">
          <p class="muted">
            Pipeline non initialisé : table <code>pipeline_stages</code> absente.
          </p>
          <p class="muted">Exécute la migration SQL pour activer le board.</p>
        </div>

      <?php else: ?>

        <div class="pipeline-columns">

          <?php foreach (($stages ?? []) as $stage): ?>
            <?php
              $stageId = (int) ($stage['id'] ?? 0);
              $cards = $grouped[$stageId] ?? [];
              $stageCount += count($cards);
            ?>

            <section class="pipeline-stage">

              <!-- HEADER COLONNE -->
              <header class="pipeline-stage-header">
                <strong><?= htmlspecialchars((string) ($stage['name'] ?? 'Étape')) ?></strong>
                <span class="badge"><?= count($cards) ?></span>
              </header>

              <!-- EMPTY -->
              <?php if ($cards === []): ?>
                <div class="empty-state small">
                  <p class="muted">Aucun prospect</p>
                </div>
              <?php endif; ?>

              <!-- CARDS -->
              <div class="stack">

                <?php foreach ($cards as $prospect): ?>
                  <article class="pipeline-card">

                    <a href="/prospects/<?= (int) $prospect['prospect_id'] ?>" class="pipeline-card-link">
                      <strong><?= htmlspecialchars((string) ($prospect['full_name'] ?? 'Prospect')) ?></strong>

                      <p class="muted">
                        <?= htmlspecialchars((string) ($prospect['activity'] ?? 'Plateforme non définie')) ?>
                      </p>

                      <small class="muted">
                        Dernière action : <?= htmlspecialchars((string) ($prospect['last_action'] ?? '—')) ?>
                      </small>
                    </a>

                    <!-- ACTION -->
                    <form method="post" action="/pipeline/<?= (int) $prospect['prospect_id'] ?>/move" class="pipeline-form-row">
                      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

                      <select name="stage_id" class="input">
                        <?php foreach (($stages ?? []) as $option): ?>
                          <option value="<?= (int) $option['id'] ?>"
                            <?= ((int) $option['id'] === (int) $prospect['stage_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $option['name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>

                      <button class="btn btn-secondary" type="submit">
                        Déplacer
                      </button>
                    </form>

                  </article>
                <?php endforeach; ?>

              </div>

            </section>

          <?php endforeach; ?>

        </div>

      <?php endif; ?>

    </div>

    <!-- FOOT INFO -->
    <div class="card">
      <p class="muted">
        <?= (int) $stageCount ?> prospects actifs dans ton pipeline
      </p>
    </div>

  </div>
</div>