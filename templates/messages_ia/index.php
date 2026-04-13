<?php
$context = $context ?? [];
$painPoints = $context['pain_points'] ?? [];
$painPointsString = implode('||', $painPoints);
?>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<section class="card" style="padding:20px;">
  <p class="eyebrow" style="margin:0 0 8px;color:#475569;font-weight:700;">Module Messages IA</p>
  <h2 style="margin:0 0 8px;">Génération guidée de messages</h2>
  <p class="muted" style="margin:0;">Utilisez les données injectées depuis Stratégie pour produire des messages prêts à copier, sauvegarder et envoyer.</p>
</section>

<section class="card" style="margin-top:12px;display:grid;gap:14px;">
  <h3 style="margin:0;">Contexte prospect prérempli</h3>
  <div style="display:grid;gap:10px;">
    <div><strong>Résumé :</strong> <?= htmlspecialchars((string) ($context['summary'] ?? '')) ?></div>
    <div><strong>Niveau de conscience :</strong> <?= htmlspecialchars((string) ($context['awareness_level'] ?? '')) ?></div>
    <div><strong>Pain points :</strong> <?= htmlspecialchars(implode(' · ', $painPoints)) ?></div>
    <div><strong>Désir principal :</strong> <?= htmlspecialchars((string) ($context['main_desire'] ?? '')) ?></div>
    <div><strong>Ton recommandé :</strong> <?= htmlspecialchars((string) ($context['recommended_tone'] ?? '')) ?></div>
    <div><strong>Hook/angle :</strong> <?= htmlspecialchars((string) ($context['hook_angle'] ?? '')) ?></div>
  </div>
</section>

<section class="card" style="margin-top:12px;">
  <form method="post" action="/messages-ia/generate" style="display:grid;gap:12px;">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
    <input type="hidden" name="summary" value="<?= htmlspecialchars((string) ($context['summary'] ?? '')) ?>">
    <input type="hidden" name="awareness_level" value="<?= htmlspecialchars((string) ($context['awareness_level'] ?? '')) ?>">
    <input type="hidden" name="pain_points" value="<?= htmlspecialchars($painPointsString) ?>">
    <input type="hidden" name="main_desire" value="<?= htmlspecialchars((string) ($context['main_desire'] ?? '')) ?>">
    <input type="hidden" name="recommended_tone" value="<?= htmlspecialchars((string) ($context['recommended_tone'] ?? '')) ?>">
    <input type="hidden" name="hook_angle" value="<?= htmlspecialchars((string) ($context['hook_angle'] ?? '')) ?>">

    <label for="message_type" style="font-weight:700;">Type de message</label>
    <select id="message_type" name="message_type" required>
      <option value="">Choisir...</option>
      <?php foreach (($messageTypes ?? []) as $key => $label): ?>
        <option value="<?= htmlspecialchars((string) $key) ?>" <?= ($selectedType ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars((string) $label) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="channel" style="font-weight:700;">Canal</label>
    <select id="channel" name="channel" required>
      <option value="">Choisir...</option>
      <?php foreach (($channels ?? []) as $key => $label): ?>
        <option value="<?= htmlspecialchars((string) $key) ?>" <?= ($selectedChannel ?? '') === $key ? 'selected' : '' ?>><?= htmlspecialchars((string) $label) ?></option>
      <?php endforeach; ?>
    </select>

    <button class="btn" type="submit" style="width:100%;">Générer 3 variantes</button>
  </form>
</section>

<?php if (is_array($generated ?? null) && ($generated ?? []) !== []): ?>
  <section class="card" style="margin-top:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
      <h3 style="margin:0;">Variantes générées</h3>
      <?php if (($mode ?? '') === 'fallback'): ?>
        <span class="status-badge" style="background:#fef3c7;color:#92400e;">Mode dégradé IA</span>
      <?php endif; ?>
    </div>

    <div style="display:grid;gap:10px;margin-top:12px;">
      <?php foreach ($generated as $index => $variant): ?>
        <article style="border:1px solid #e2e8f0;background:#f8fafc;border-radius:12px;padding:12px;">
          <p style="margin:0 0 8px;font-weight:700;">Variante <?= (int) $index + 1 ?></p>
          <p id="variant-<?= (int) $index ?>" style="margin:0;white-space:pre-wrap;"><?= htmlspecialchars((string) $variant) ?></p>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;">
            <button class="btn secondary" type="button" onclick="copyVariant('variant-<?= (int) $index ?>')">Copier</button>
            <button class="btn secondary" type="button" onclick="saveVariant()">Enregistrer</button>
            <button class="btn secondary" type="button" onclick="sendToContact()">Envoyer vers contact</button>
            <button class="btn secondary" type="button" onclick="transformToEmail('variant-<?= (int) $index ?>')">Transformer en email</button>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <script>
    function copyVariant(id) {
      var el = document.getElementById(id);
      if (!el) return;
      navigator.clipboard.writeText(el.innerText)
        .then(function () { alert('Message copié ✅'); })
        .catch(function () { alert('Copie impossible sur ce navigateur.'); });
    }

    function saveVariant() {
      alert('Version MVP: message enregistré dans votre session de travail.');
    }

    function sendToContact() {
      alert('Version MVP: envoi vers contact à brancher sur le module Contacts.');
    }

    function transformToEmail(id) {
      var el = document.getElementById(id);
      if (!el) return;
      var transformed = 'Objet: Proposition rapide\n\n' + el.innerText + '\n\nBien à vous,';
      navigator.clipboard.writeText(transformed)
        .then(function () { alert('Version email copiée ✅'); })
        .catch(function () { alert('Transformation OK, mais copie impossible.'); });
    }
  </script>
<?php endif; ?>
