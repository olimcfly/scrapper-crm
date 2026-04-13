<?php
$analysisData = is_array($analysis ?? null) ? $analysis : [];
?>

<?php if (!empty($successMessage)): ?>
  <div class="alert success"><?= htmlspecialchars((string) $successMessage) ?></div>
<?php endif; ?>
<?php if (!empty($warningMessage)): ?>
  <div class="alert warning"><?= htmlspecialchars((string) $warningMessage) ?></div>
<?php endif; ?>

<section class="card">
  <p class="eyebrow">Module Messages IA</p>
  <h2 style="margin:0 0 8px;">Brouillons de messages reliés à l’analyse</h2>
</section>

<?php if ($analysisData === [] || (int) ($analysisId ?? 0) <= 0): ?>
  <section class="card">
    <p class="muted">Aucune analyse chargée. Lancez d’abord une analyse prospect.</p>
    <a class="btn" href="/strategie">Aller sur Stratégie</a>
  </section>
<?php else: ?>
  <section class="card" style="display:grid;gap:10px;">
    <h3 style="margin:0;">Analyse source #<?= (int) $analysisId ?></h3>
    <p class="muted" style="margin:0;"><?= htmlspecialchars((string) ($analysisData['summary'] ?? '')) ?></p>
    <p class="muted" style="margin:0;"><strong>Niveau:</strong> <?= htmlspecialchars((string) ($analysisData['awareness_level'] ?? 'N/A')) ?></p>
  </section>

  <section class="card">
    <form method="post" action="/messages-ia/generer" style="display:grid;gap:12px;">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
      <label>Type de message
        <select name="message_type">
          <?php foreach (['dm' => 'DM', 'relance' => 'Relance', 'reponse' => 'Réponse'] as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($selectedType ?? 'dm') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Canal
        <select name="channel">
          <?php foreach (['whatsapp' => 'WhatsApp', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'email' => 'Email', 'sms' => 'SMS'] as $value => $label): ?>
            <option value="<?= htmlspecialchars($value) ?>" <?= (($selectedChannel ?? 'whatsapp') === $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="btn" type="submit">Générer un brouillon</button>
    </form>

    <?php if (!empty($draftText)): ?>
      <h4>Brouillon rechargé</h4>
      <pre style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;"><?= htmlspecialchars((string) $draftText) ?></pre>
    <?php endif; ?>
  </section>
<?php endif; ?>

<section class="card">
  <h3 style="margin-top:0;">Historique des brouillons messages</h3>
  <?php if (($history ?? []) === []): ?>
    <p class="muted">Aucun brouillon message pour le moment.</p>
  <?php endif; ?>
  <?php foreach (($history ?? []) as $item): ?>
    <article style="border-top:1px solid #e2e8f0;padding-top:10px;margin-top:10px;">
      <p class="muted" style="margin:0 0 8px;"><?= htmlspecialchars((string) $item['created_at']) ?> · <?= htmlspecialchars((string) $item['message_type']) ?> · <?= htmlspecialchars((string) $item['channel']) ?> · analyse #<?= (int) $item['analysis_id'] ?></p>
      <p style="white-space:pre-wrap;margin:0 0 10px;"><?= htmlspecialchars((string) $item['message_text']) ?></p>
      <div class="row">
        <a class="btn secondary" href="/messages-ia?draft_id=<?= (int) $item['id'] ?>">Rouvrir</a>
        <form method="post" action="/messages-ia/dupliquer">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
          <input type="hidden" name="draft_id" value="<?= (int) $item['id'] ?>">
          <button type="submit" class="btn">Dupliquer</button>
        </form>
      </div>
    </article>
  <?php endforeach; ?>
</section>
