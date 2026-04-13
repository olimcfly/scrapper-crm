<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1><?= htmlspecialchars($title ?? 'Prospect') ?></h1>
      <p class="subtitle">Créer ou modifier un prospect avec stratégie intégrée</p>
    </div>

    <!-- ERRORS -->
    <?php if (!empty($errors)): ?>
      <div class="card">
        <div class="text-error">
          <?php foreach ($errors as $e): ?>
            <p>• <?= htmlspecialchars($e) ?></p>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="post" action="<?= htmlspecialchars($action ?? '/prospects/create') ?>" class="stack">

      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">

      <!-- INFOS -->
      <div class="card">
        <div class="card-header">
          <h3>Informations prospect</h3>
        </div>

        <div class="grid">

          <div class="form-group">
            <label>Prénom</label>
            <input class="input" name="first_name" value="<?= htmlspecialchars($prospect['first_name'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Nom</label>
            <input class="input" name="last_name" value="<?= htmlspecialchars($prospect['last_name'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Société</label>
            <input class="input" name="business_name" value="<?= htmlspecialchars($prospect['business_name'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Activité</label>
            <input class="input" name="activity" value="<?= htmlspecialchars($prospect['activity'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Ville</label>
            <input class="input" name="city" value="<?= htmlspecialchars($prospect['city'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Pays</label>
            <input class="input" name="country" value="<?= htmlspecialchars($prospect['country'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Email pro</label>
            <input class="input" name="professional_email" value="<?= htmlspecialchars($prospect['professional_email'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Téléphone pro</label>
            <input class="input" name="professional_phone" value="<?= htmlspecialchars($prospect['professional_phone'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Site</label>
            <input class="input" name="website" value="<?= htmlspecialchars($prospect['website'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Score</label>
            <input class="input" type="number" min="0" max="100" name="score" value="<?= htmlspecialchars((string)($prospect['score'] ?? 0)) ?>">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select class="input" name="status_id">
              <?php foreach (($statuses ?? []) as $s): ?>
                <option value="<?= (int)$s['id'] ?>" <?= ((int)($prospect['status_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Source</label>
            <select class="input" name="source_id">
              <?php foreach (($sources ?? []) as $s): ?>
                <option value="<?= (int)$s['id'] ?>" <?= ((int)($prospect['source_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <div class="form-group">
          <label>Résumé</label>
          <textarea class="input" name="notes_summary"><?= htmlspecialchars($prospect['notes_summary'] ?? '') ?></textarea>
        </div>

      </div>

      <!-- STRATEGIE -->
      <div class="card">
        <div class="card-header">
          <h3>Stratégie par prospect</h3>
        </div>

        <div class="grid">

          <div class="form-group">
            <label>Objectif de contact</label>
            <input class="input" name="objectif_contact" value="<?= htmlspecialchars($prospect['objectif_contact'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Prochaine action</label>
            <input class="input" name="prochaine_action" value="<?= htmlspecialchars($prospect['prochaine_action'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Date prochaine action</label>
            <input class="input" type="date" name="date_prochaine_action" value="<?= htmlspecialchars((string)($prospect['date_prochaine_action'] ?? '')) ?>">
          </div>

          <div class="form-group">
            <label>Canal prioritaire</label>
            <select class="input" name="canal_prioritaire">
              <option value="">-- Sélectionner --</option>
              <?php foreach (['appel' => 'Appel', 'email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= (($prospect['canal_prioritaire'] ?? '') === $value) ? 'selected' : '' ?>>
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Niveau priorité</label>
            <select class="input" name="niveau_priorite">
              <?php foreach (['faible' => 'Faible', 'moyen' => 'Moyen', 'eleve' => 'Élevé'] as $value => $label): ?>
                <option value="<?= $value ?>" <?= (($prospect['niveau_priorite'] ?? 'moyen') === $value) ? 'selected' : '' ?>>
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <div class="form-group">
          <label>Blocages</label>
          <textarea class="input" name="blocages"><?= htmlspecialchars($prospect['blocages'] ?? '') ?></textarea>
        </div>

      </div>

      <!-- CTA -->
      <div class="card">
        <button class="btn btn-primary" type="submit">
          Enregistrer le prospect
        </button>
      </div>

    </form>

  </div>
</div>