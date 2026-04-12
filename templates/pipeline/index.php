<?php
$stageCount = 0;
?>

<?php if (!empty($successMessage)): ?>
  <div class="global-state loading"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $successMessage) ?></p></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="global-state error"><span class="state-dot" aria-hidden="true"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div>
<?php endif; ?>

<div class="card" style="padding:12px;overflow-x:auto;">
  <?php if (($pipelineStagesAvailable ?? true) === false): ?>
    <p class="muted" style="margin:0;">Pipeline non initialisé : table <code>pipeline_stages</code> absente. Exécutez la migration SQL pour activer le board.</p>
  <?php else: ?>
    <div style="display:flex;gap:12px;min-width:max-content;align-items:flex-start;">
      <?php foreach (($stages ?? []) as $stage): ?>
        <?php $stageId = (int) ($stage['id'] ?? 0); $cards = $grouped[$stageId] ?? []; $stageCount += count($cards); ?>
        <section style="width:280px;max-width:82vw;background:#f8fafc;border:1px solid #dbe3ef;border-radius:12px;padding:10px;">
          <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <strong><?= htmlspecialchars((string) ($stage['name'] ?? 'Étape')) ?></strong>
            <span class="status-badge status-placeholder"><?= count($cards) ?></span>
          </header>

          <?php if ($cards === []): ?>
            <p class="muted" style="font-size:13px;">Aucun prospect ici.</p>
          <?php endif; ?>

          <?php foreach ($cards as $prospect): ?>
            <article style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:8px;">
              <a href="/prospects/<?= (int) $prospect['prospect_id'] ?>" style="text-decoration:none;color:#0f172a;">
                <strong style="display:block;"><?= htmlspecialchars((string) ($prospect['full_name'] ?? 'Prospect')) ?></strong>
                <small class="muted" style="display:block;margin:4px 0;"><?= htmlspecialchars((string) ($prospect['activity'] ?? 'Plateforme non définie')) ?></small>
                <small>Dernière action : <?= htmlspecialchars((string) ($prospect['last_action'] ?? '—')) ?></small>
              </a>

              <form method="post" action="/pipeline/<?= (int) $prospect['prospect_id'] ?>/move" style="margin-top:8px;">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                <div style="display:flex;gap:6px;">
                  <select name="stage_id" style="font-size:12px;padding:7px;">
                    <?php foreach (($stages ?? []) as $option): ?>
                      <option value="<?= (int) $option['id'] ?>" <?= ((int) $option['id'] === (int) $prospect['stage_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $option['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn compact" type="submit">Changer étape</button>
                </div>
              </form>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <p class="muted" style="margin:0;">Pipeline mobile-first actif · <?= (int) $stageCount ?> prospects visibles.</p>
</div>
