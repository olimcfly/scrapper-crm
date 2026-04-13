<?php

function safe_array($value) {
  return is_array($value) ? $value : [];
}

function safe_string($value) {
  return htmlspecialchars((string) ($value ?? ''));
}

$analysisData = safe_array($analysis ?? null);
$optionsData = safe_array($options ?? null);
$generatedData = is_array($generated ?? null) ? $generated : null;
$historyData = safe_array($history ?? null);

$contentTypeValue = (string) ($optionsData['content_type'] ?? 'post');
$channelValue = (string) ($optionsData['channel'] ?? 'linkedin');
$objectiveValue = (string) ($optionsData['objective'] ?? 'attirer');
$toneValue = (string) ($optionsData['tone'] ?? 'simple');
$lengthValue = (string) ($optionsData['length'] ?? 'moyenne');
?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Génération de contenu</h1>
      <p class="subtitle">Créer du contenu à partir de la stratégie prospect</p>
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

    <?php if ($analysisData === []): ?>

      <div class="card">
        <div class="empty-state">
          <p>Aucune analyse disponible</p>
          <a href="/strategie" class="btn btn-primary">Créer une analyse</a>
        </div>
      </div>

    <?php else: ?>

      <div class="grid">

        <!-- LEFT -->
        <div class="stack">

          <!-- BRIEF -->
          <div class="card">
            <div class="card-header">
              <h3>Brief prospect</h3>
            </div>

            <div class="stack-sm">

              <div class="card small">
                <strong>Résumé</strong>
                <p><?= nl2br(safe_string($analysisData['summary'] ?? '')) ?></p>
              </div>

              <div class="card small">
                <strong>Niveau de conscience</strong>
                <p><?= safe_string($analysisData['awareness_level'] ?? '') ?></p>
              </div>

              <div class="card small">
                <strong>Pain points</strong>
                <p><?= safe_string(implode(' • ', safe_array($analysisData['pain_points'] ?? null))) ?></p>
              </div>

              <div class="card small">
                <strong>Désirs</strong>
                <p><?= safe_string(implode(' • ', safe_array($analysisData['desires'] ?? null))) ?></p>
              </div>

            </div>
          </div>

          <!-- HISTORY -->
          <div class="card">
            <div class="card-header">
              <h3>Historique</h3>
            </div>

            <?php if ($historyData === []): ?>

              <div class="empty-state">
                <p>Aucun contenu généré</p>
              </div>

            <?php else: ?>

              <div class="stack-sm">
                <?php foreach ($historyData as $item): ?>
                  <article class="card small">
                    <p class="muted">
                      <?= safe_string($item['created_at'] ?? '') ?>
                      · <?= safe_string($item['channel'] ?? '') ?>
                    </p>

                    <p><?= nl2br(safe_string($item['generated_content'] ?? '')) ?></p>

                    <a href="/contenu?draft_id=<?= (int) ($item['id'] ?? 0) ?>" class="btn btn-secondary">
                      Rouvrir
                    </a>
                  </article>
                <?php endforeach; ?>
              </div>

            <?php endif; ?>

          </div>

        </div>

        <!-- RIGHT -->
        <div class="stack">

          <!-- FORM -->
          <div class="card">
            <div class="card-header">
              <h3>Générer un contenu</h3>
            </div>

            <form method="post" action="/contenu/generer" class="stack">

              <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">

              <div class="form-group">
                <label>Type</label>
                <select name="content_type" class="input">
                  <option value="post" <?= $contentTypeValue === 'post' ? 'selected' : '' ?>>Post</option>
                  <option value="email" <?= $contentTypeValue === 'email' ? 'selected' : '' ?>>Email</option>
                  <option value="message_court" <?= $contentTypeValue === 'message_court' ? 'selected' : '' ?>>Message court</option>
                </select>
              </div>

              <div class="form-group">
                <label>Canal</label>
                <select name="channel" class="input">
                  <option value="linkedin" <?= $channelValue === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                  <option value="facebook" <?= $channelValue === 'facebook' ? 'selected' : '' ?>>Facebook</option>
                  <option value="instagram" <?= $channelValue === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                  <option value="email" <?= $channelValue === 'email' ? 'selected' : '' ?>>Email</option>
                </select>
              </div>

              <div class="form-group">
                <label>Objectif</label>
                <select name="objective" class="input">
                  <option value="attirer" <?= $objectiveValue === 'attirer' ? 'selected' : '' ?>>Attirer</option>
                  <option value="convertir" <?= $objectiveValue === 'convertir' ? 'selected' : '' ?>>Convertir</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">
                Générer
              </button>

            </form>
          </div>

          <!-- RESULT -->
          <?php if ($generatedData !== null): ?>
            <div class="card">
              <div class="card-header">
                <h3>Résultat</h3>
              </div>

              <div class="card small">
                <?= nl2br(safe_string($generatedData['content'] ?? '')) ?>
              </div>
            </div>
          <?php endif; ?>

        </div>

      </div>

    <?php endif; ?>

  </div>
</div>