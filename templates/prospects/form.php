<div class="card">
  <h2><?= htmlspecialchars($title ?? 'Prospect') ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="errors">
      <?php foreach ($errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= htmlspecialchars($action ?? '/prospects/create') ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
    <div class="row">
      <div><label>Prénom</label><input name="first_name" value="<?= htmlspecialchars($prospect['first_name'] ?? '') ?>"></div>
      <div><label>Nom</label><input name="last_name" value="<?= htmlspecialchars($prospect['last_name'] ?? '') ?>"></div>
    </div>
    <div class="row">
      <div><label>Société</label><input name="business_name" value="<?= htmlspecialchars($prospect['business_name'] ?? '') ?>"></div>
      <div><label>Activité</label><input name="activity" value="<?= htmlspecialchars($prospect['activity'] ?? '') ?>"></div>
    </div>
    <div class="row">
      <div><label>Ville</label><input name="city" value="<?= htmlspecialchars($prospect['city'] ?? '') ?>"></div>
      <div><label>Pays</label><input name="country" value="<?= htmlspecialchars($prospect['country'] ?? '') ?>"></div>
    </div>
    <div class="row">
      <div><label>Email pro</label><input name="professional_email" value="<?= htmlspecialchars($prospect['professional_email'] ?? '') ?>"></div>
      <div><label>Téléphone pro</label><input name="professional_phone" value="<?= htmlspecialchars($prospect['professional_phone'] ?? '') ?>"></div>
    </div>
    <div class="row">
      <div><label>Site</label><input name="website" value="<?= htmlspecialchars($prospect['website'] ?? '') ?>"></div>
      <div><label>Score (0-100)</label><input name="score" type="number" min="0" max="100" value="<?= htmlspecialchars((string)($prospect['score'] ?? 0)) ?>"></div>
    </div>
    <div class="row">
      <div><label>Status</label>
        <select name="status_id">
          <?php foreach (($statuses ?? []) as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= ((int)($prospect['status_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Source</label>
        <select name="source_id">
          <?php foreach (($sources ?? []) as $s): ?>
            <option value="<?= (int)$s['id'] ?>" <?= ((int)($prospect['source_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div><label>Résumé</label><textarea name="notes_summary"><?= htmlspecialchars($prospect['notes_summary'] ?? '') ?></textarea></div>
    <hr style="border:none;border-top:1px solid #eef2f7;margin:16px 0;">
    <h3>Stratégie par prospect</h3>
    <div><label>Objectif de contact</label><input name="objectif_contact" value="<?= htmlspecialchars($prospect['objectif_contact'] ?? '') ?>"></div>
    <div><label>Prochaine action</label><input name="prochaine_action" value="<?= htmlspecialchars($prospect['prochaine_action'] ?? '') ?>"></div>
    <div class="row">
      <div><label>Date prochaine action</label><input type="date" name="date_prochaine_action" value="<?= htmlspecialchars((string)($prospect['date_prochaine_action'] ?? '')) ?>"></div>
      <div><label>Canal prioritaire</label>
        <select name="canal_prioritaire">
          <option value="">-- Sélectionner --</option>
          <?php foreach (['appel' => 'Appel', 'email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp'] as $value => $label): ?>
            <option value="<?= $value ?>" <?= (($prospect['canal_prioritaire'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="row">
      <div><label>Niveau priorité</label>
        <select name="niveau_priorite">
          <?php foreach (['faible' => 'Faible', 'moyen' => 'Moyen', 'eleve' => 'Élevé'] as $value => $label): ?>
            <option value="<?= $value ?>" <?= (($prospect['niveau_priorite'] ?? 'moyen') === $value) ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Blocages</label><textarea name="blocages"><?= htmlspecialchars($prospect['blocages'] ?? '') ?></textarea></div>
    </div>
    <p><button class="btn" type="submit">Enregistrer</button></p>
  </form>
</div>
