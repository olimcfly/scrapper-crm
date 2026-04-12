<?php
$typeLabels = [
  'post' => 'Post (LinkedIn / Facebook)',
  'video' => 'Script vidéo (TikTok / Reels)',
  'story' => 'Story courte',
  'dm' => 'Message DM',
];
?>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<div class="card">
  <p class="muted" style="margin:0 0 6px;">Prospect</p>
  <h2 style="margin:0;"><?= htmlspecialchars((string) ($prospect['full_name'] ?? '')) ?></h2>
  <p class="muted" style="margin:8px 0 0;">🎯 Basé sur son niveau de conscience: <strong><?= htmlspecialchars((string) ($awarenessLevel ?? '')) ?></strong></p>
  <p style="margin:10px 0 0;"><span style="background:#fee2e2;color:#9f1239;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700;">🔥 Recommandé pour ce prospect : <?= htmlspecialchars((string) ($recommendedType ?? '')) ?></span></p>
</div>

<?php if (($type ?? '') === ''): ?>
  <div class="card">
    <h3 style="margin-top:0;">Choix du type de contenu</h3>
    <div class="row">
      <?php foreach ($typeLabels as $typeKey => $label): ?>
        <a class="btn" style="width:100%;text-align:center;" href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents?type=<?= urlencode($typeKey) ?>"><?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
<?php elseif (($generated ?? null) === null): ?>
  <div class="card">
    <h3 style="margin-top:0;">Génération</h3>
    <p class="muted">Type choisi: <strong><?= htmlspecialchars($typeLabels[$type] ?? $type) ?></strong></p>

    <form method="post" action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
      <input type="hidden" name="type" value="<?= htmlspecialchars((string) $type) ?>">

      <?php if (($type ?? '') === 'dm'): ?>
        <label for="interaction" style="display:block;margin-bottom:6px;">Interaction précédente</label>
        <input id="interaction" name="interaction" type="text" placeholder="Ex: Like sur votre dernier post" value="Like récent sur un post.">
      <?php endif; ?>

      <button class="btn" type="submit" style="margin-top:12px;width:100%;">Générer</button>
    </form>
  </div>
<?php else: ?>
  <div class="card">
    <h3 style="margin-top:0;">Résultat prêt à poster</h3>
    <p class="muted">Type: <strong><?= htmlspecialchars($typeLabels[$generated['type'] ?? ''] ?? (string) ($generated['type'] ?? '')) ?></strong></p>

    <?php if (!empty($generated['hook'])): ?>
      <p class="muted" style="margin-bottom:4px;">Hook</p>
      <p style="margin-top:0;font-weight:700;"><?= nl2br(htmlspecialchars((string) $generated['hook'])) ?></p>
    <?php endif; ?>

    <p class="muted" style="margin-bottom:4px;">Contenu</p>
    <p id="generatedContent" style="white-space:pre-wrap;"><?= htmlspecialchars((string) ($generated['content'] ?? '')) ?></p>

    <?php if (!empty($generated['angle'])): ?>
      <p class="muted" style="margin-bottom:4px;">Angle</p>
      <p style="margin-top:0;"><?= htmlspecialchars((string) $generated['angle']) ?></p>
    <?php endif; ?>

    <div class="row" style="margin-top:12px;">
      <button class="btn secondary" type="button" onclick="copyGeneratedContent()">Copier</button>
      <form method="post" action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <input type="hidden" name="type" value="<?= htmlspecialchars((string) ($generated['type'] ?? $type)) ?>">
        <button class="btn" type="submit">Regénérer</button>
      </form>
    </div>
  </div>

  <script>
    async function copyGeneratedContent() {
      const target = document.getElementById('generatedContent');
      if (!target) return;
      const text = target.innerText;
      try {
        await navigator.clipboard.writeText(text);
        alert('Contenu copié ✅');
      } catch (e) {
        alert('Copie impossible sur ce navigateur.');
      }
    }
  </script>
<?php endif; ?>

<div class="card">
  <a class="btn secondary" href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>">Retour au prospect</a>
</div>
