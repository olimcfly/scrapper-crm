<?php
$typeLabels = [
  'post' => 'Post réseau social',
  'email' => 'Email',
  'message_court' => 'Message court (DM/WhatsApp/SMS)',
];

$variants = is_array($payload['variants'] ?? null) ? $payload['variants'] : [];
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
  <p class="muted" style="margin:8px 0 0;">🎯 Niveau de conscience: <strong><?= htmlspecialchars((string) ($awarenessLevel ?? '')) ?></strong></p>
  <p style="margin:10px 0 0;"><span style="background:#fee2e2;color:#9f1239;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700;">🔥 Type recommandé : <?= htmlspecialchars((string) ($recommendedType ?? '')) ?></span></p>
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

    <form method="post" action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate" style="display:grid;gap:10px;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
      <input type="hidden" name="type" value="<?= htmlspecialchars((string) $type) ?>">

      <label for="awareness_level">Niveau de conscience</label>
      <input id="awareness_level" name="awareness_level" type="text" value="<?= htmlspecialchars((string) ($awarenessLevel ?? '')) ?>">

      <label for="pain_points">Pain points (virgule ou ligne)</label>
      <textarea id="pain_points" name="pain_points" rows="2" placeholder="ex: agenda vide, difficulté à fidéliser"><?= htmlspecialchars((string) ($prospect['blocages'] ?? '')) ?></textarea>

      <label for="desires">Désirs (virgule ou ligne)</label>
      <textarea id="desires" name="desires" rows="2" placeholder="ex: plus de rendez-vous réguliers"><?= htmlspecialchars((string) ($prospect['notes_summary'] ?? '')) ?></textarea>

      <label for="angle">Angle choisi</label>
      <input id="angle" name="angle" type="text" placeholder="ex: éducatif terrain">

      <label for="hook">Hook choisi</label>
      <input id="hook" name="hook" type="text" placeholder="ex: Vous remplissez votre planning mais pas vos séances ?">

      <label for="channel">Contexte canal</label>
      <input id="channel" name="channel" type="text" placeholder="ex: suite à un commentaire Instagram" value="interaction récente">

      <button class="btn" type="submit" style="margin-top:4px;width:100%;">Générer 3 variantes</button>
    </form>
  </div>
<?php else: ?>
  <div class="card">
    <h3 style="margin-top:0;">Brouillons générés</h3>
    <p class="muted">Type: <strong><?= htmlspecialchars($typeLabels[$generated['type'] ?? ''] ?? (string) ($generated['type'] ?? '')) ?></strong></p>
    <p class="muted">Variantes: <strong><?= (int) count($variants) ?></strong></p>

    <?php foreach ($variants as $index => $variant): ?>
      <?php
        $composed = trim(
          (string) ($variant['title'] ?? '') . "\n" .
          (string) ($variant['subject'] ?? '') . "\n" .
          (string) ($variant['opening'] ?? '') . "\n\n" .
          (string) ($variant['body'] ?? '') . "\n\n" .
          (string) ($variant['closing'] ?? '') . "\n" .
          (string) ($variant['cta'] ?? '')
        );
      ?>
      <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px;margin-bottom:12px;">
        <p style="margin:0 0 8px;font-weight:700;"><?= htmlspecialchars((string) ($variant['label'] ?? ('Variante ' . ($index + 1)))) ?>
          <?php if (!empty($variant['format'])): ?><span class="muted"> · <?= htmlspecialchars((string) $variant['format']) ?></span><?php endif; ?>
        </p>
        <pre id="variant-<?= (int) $index ?>" style="white-space:pre-wrap;margin:0;font-family:inherit;"><?= htmlspecialchars($composed) ?></pre>

        <div class="row" style="margin-top:10px;">
          <button class="btn secondary" type="button" onclick="copyVariant(<?= (int) $index ?>)">Copier</button>
          <a class="btn" href="/messages-ia" onclick="copyVariant(<?= (int) $index ?>)">Utiliser pour message</a>
          <a class="btn" href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>" onclick="copyVariant(<?= (int) $index ?>)">Utiliser pour contact</a>
        </div>
      </article>
    <?php endforeach; ?>

    <form method="post" action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
      <input type="hidden" name="type" value="<?= htmlspecialchars((string) ($generated['type'] ?? $type)) ?>">
      <button class="btn" type="submit">Régénérer</button>
    </form>
  </div>

  <div class="card">
    <h3 style="margin-top:0;">Contexte utilisé pour la génération</h3>
    <pre style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e2e8f0;padding:10px;border-radius:8px;"><?= htmlspecialchars(json_encode($contextUsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}') ?></pre>
  </div>

  <script>
    async function copyVariant(index) {
      const target = document.getElementById('variant-' + index);
      if (!target) return;
      const text = target.innerText;
      try {
        await navigator.clipboard.writeText(text);
      } catch (e) {
        alert('Copie impossible sur ce navigateur.');
      }
    }
  </script>
<?php endif; ?>

<div class="card">
  <a class="btn secondary" href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>">Retour au prospect</a>
</div>
