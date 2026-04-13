<?php
$analysisData = is_array($analysis ?? null) ? $analysis : [];
$optionsData = is_array($options ?? null) ? $options : [];
$generatedData = is_array($generated ?? null) ? $generated : null;

$contentTypeLabels = [
  'post' => 'Post',
  'email' => 'Email',
  'message_court' => 'Message court',
];
$channelLabels = [
  'facebook' => 'Facebook',
  'instagram' => 'Instagram',
  'linkedin' => 'LinkedIn',
  'tiktok' => 'TikTok',
  'email' => 'Email',
  'whatsapp' => 'WhatsApp',
  'sms' => 'SMS',
];
$objectiveLabels = [
  'attirer' => 'Attirer',
  'faire_reagir' => 'Faire réagir',
  'rassurer' => 'Rassurer',
  'prendre_rendez_vous' => 'Prendre rendez-vous',
  'relancer' => 'Relancer',
  'convertir' => 'Convertir',
];
$toneLabels = [
  'simple' => 'Simple',
  'directe' => 'Directe',
  'rassurante' => 'Rassurante',
  'experte' => 'Experte',
  'chaleureuse' => 'Chaleureuse',
];
$lengthLabels = [
  'courte' => 'Courte',
  'moyenne' => 'Moyenne',
  'longue' => 'Longue',
];
?>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<section class="card page-lead">
  <p class="eyebrow">Module Contenu</p>
  <h2 style="margin:0 0 8px;">Génération guidée à partir de la stratégie prospect</h2>
  <p style="color:#cbd5e1;">Le brief est préchargé pour accélérer la création de contenu orienté conversion.</p>
</section>

<?php if ($analysisData === []): ?>
  <section class="card">
    <h3 style="margin-top:0;">Aucune analyse chargée</h3>
    <p class="muted">Commencez sur Stratégie pour analyser un prospect puis revenez ici avec "Générer du contenu".</p>
    <a class="btn" href="/strategie" style="margin-top:12px;">Aller sur Stratégie</a>
  </section>
<?php else: ?>
  <section class="card">
    <h3 style="margin-top:0;">Brief prospect préchargé</h3>
    <div style="display:grid;gap:10px;">
      <div><strong>Résumé</strong><p class="muted"><?= nl2br(htmlspecialchars((string) ($analysisData['summary'] ?? ''))) ?></p></div>
      <div><strong>Niveau de conscience</strong><p class="muted"><?= htmlspecialchars((string) ($analysisData['awareness_level'] ?? 'N/A')) ?></p></div>
      <div><strong>Pain points</strong><p class="muted"><?= htmlspecialchars(implode(' • ', $analysisData['pain_points'] ?? [])) ?></p></div>
      <div><strong>Désirs</strong><p class="muted"><?= htmlspecialchars(implode(' • ', $analysisData['desires'] ?? [])) ?></p></div>
      <div><strong>Angles de contenu</strong><p class="muted"><?= htmlspecialchars(implode(' • ', $analysisData['content_angles'] ?? [])) ?></p></div>
      <div><strong>Hooks</strong><p class="muted"><?= htmlspecialchars(implode(' • ', $analysisData['recommended_hooks'] ?? [])) ?></p></div>
    </div>
  </section>

  <section class="card">
    <h3 style="margin-top:0;">Formulaire de génération</h3>
    <form method="post" action="/contenu/generer" style="display:grid;gap:12px;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

      <label>Type de contenu
        <select name="content_type">
          <?php foreach ($contentTypeLabels as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($optionsData['content_type'] ?? '') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Plateforme / canal
        <select name="channel">
          <?php foreach ($channelLabels as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($optionsData['channel'] ?? '') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Objectif
        <select name="objective">
          <?php foreach ($objectiveLabels as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($optionsData['objective'] ?? '') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Tonalité
        <select name="tone">
          <?php foreach ($toneLabels as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($optionsData['tone'] ?? '') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Longueur
        <select name="length">
          <?php foreach ($lengthLabels as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($optionsData['length'] ?? '') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Variante
        <input type="text" name="variant" value="Variante 1" maxlength="120">
      </label>

      <button class="btn" type="submit" style="width:100%;">Générer</button>
    </form>
  </section>

  <?php if ($generatedData !== null): ?>
    <section class="card">
      <h3 style="margin-top:0;">Contenu généré</h3>
      <?php if (!empty($generatedData['meta']['warning'])): ?>
        <p class="muted" style="margin-bottom:8px;color:#92400e;"><?= htmlspecialchars((string) $generatedData['meta']['warning']) ?></p>
      <?php endif; ?>
      <pre style="white-space:pre-wrap;margin:0;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;"><?= htmlspecialchars((string) ($generatedData['content'] ?? '')) ?></pre>
    </section>
  <?php endif; ?>

  <section class="card">
    <h3 style="margin-top:0;">Historique des brouillons contenus</h3>
    <?php if (($history ?? []) === []): ?>
      <p class="muted">Aucun brouillon enregistré.</p>
    <?php endif; ?>
    <?php foreach (($history ?? []) as $item): ?>
      <article style="border-top:1px solid #e2e8f0;padding-top:10px;margin-top:10px;">
        <p class="muted" style="margin:0 0 8px;"><?= htmlspecialchars((string) $item['created_at']) ?> · <?= htmlspecialchars((string) $item['content_type']) ?> · <?= htmlspecialchars((string) $item['channel']) ?> · <?= htmlspecialchars((string) $item['tone']) ?> · analyse #<?= (int) $item['analysis_id'] ?></p>
        <p style="white-space:pre-wrap;margin:0 0 10px;"><?= htmlspecialchars((string) $item['generated_content']) ?></p>
        <div class="row">
          <a class="btn secondary" href="/contenu?draft_id=<?= (int) $item['id'] ?>">Rouvrir</a>
          <form method="post" action="/contenu/dupliquer">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
            <input type="hidden" name="draft_id" value="<?= (int) $item['id'] ?>">
            <button class="btn" type="submit">Dupliquer</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </section>
<?php endif; ?>
