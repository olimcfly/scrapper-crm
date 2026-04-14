<div class="page settings-page">
  <div class="container stack-md">
    <section class="card settings-hero" aria-labelledby="settings-title">
      <div class="settings-hero__content">
        <p class="settings-kicker">Centre de configuration</p>
        <h1 id="settings-title">Paramètres</h1>
        <p class="subtitle">Configure les emails, API, scrapers, utilisateurs et préférences de ton espace.</p>
      </div>
      <div class="settings-hero__actions">
        <button class="btn btn-secondary" type="button">Tester les connexions</button>
        <button class="btn btn-primary" type="button">Enregistrer les modifications</button>
      </div>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="identity-title">
      <div class="card-header">
        <div>
          <h2 id="identity-title">Identité de l’espace</h2>
          <p class="settings-note">Personnalise l’expéditeur et la signature utilisée dans les campagnes CRM.</p>
        </div>
        <span class="settings-badge is-active">Actif</span>
      </div>
      <div class="settings-grid two-cols">
        <label class="form-field"><span>Nom entreprise</span><input type="text" value="Acme CRM" /></label>
        <label class="form-field"><span>Nom affiché expéditeur</span><input type="text" value="Equipe Growth Acme" /></label>
        <label class="form-field"><span>Prénom</span><input type="text" value="Nina" /></label>
        <label class="form-field"><span>Nom</span><input type="text" value="Martin" /></label>
        <label class="form-field"><span>Fonction</span><input type="text" value="Head of Sales" /></label>
        <label class="form-field"><span>Téléphone</span><input type="text" value="+33 6 12 34 56 78" /></label>
        <label class="form-field"><span>Email</span><input type="email" value="nina@acme-crm.io" /></label>
        <label class="form-field"><span>Site web</span><input type="text" value="https://acme-crm.io" /></label>
        <label class="form-field settings-grid-full"><span>Adresse</span><input type="text" value="22 rue de la Performance, 75008 Paris" /></label>
        <label class="form-field settings-grid-full"><span>Signature email texte</span><textarea>Bien à vous,
Nina Martin
Head of Sales — Acme CRM</textarea></label>
        <label class="form-field settings-grid-full"><span>Signature email HTML</span><textarea>&lt;strong&gt;Nina Martin&lt;/strong&gt;&lt;br&gt;Head of Sales — Acme CRM&lt;br&gt;&lt;a href="https://acme-crm.io"&gt;acme-crm.io&lt;/a&gt;</textarea></label>
      </div>
      <div class="settings-preview">
        <p class="settings-preview__label">Aperçu signature</p>
        <div class="settings-preview__box">
          <p><strong>Nina Martin</strong></p>
          <p>Head of Sales — Acme CRM</p>
          <p><a href="#">acme-crm.io</a> · +33 6 12 34 56 78</p>
        </div>
      </div>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="smtp-title">
      <div class="card-header">
        <div>
          <h2 id="smtp-title">Emails &amp; SMTP</h2>
          <p class="settings-note">Prépare la connexion SMTP et valide la délivrabilité avant envoi.</p>
        </div>
        <span class="settings-badge is-warning">Non configuré</span>
      </div>
      <div class="settings-grid two-cols">
        <label class="form-field"><span>Host</span><input type="text" placeholder="smtp.provider.com" /></label>
        <label class="form-field"><span>Port</span><input type="text" value="587" /></label>
        <label class="form-field"><span>Encryption</span><select><option>TLS</option><option>SSL</option><option>Aucune</option></select></label>
        <label class="form-field"><span>Username</span><input type="text" placeholder="apikey" /></label>
        <label class="form-field"><span>Password</span><input type="password" value="********" /></label>
        <label class="form-field"><span>Email d’envoi</span><input type="email" placeholder="contact@entreprise.com" /></label>
        <label class="form-field"><span>Email de réponse</span><input type="email" placeholder="support@entreprise.com" /></label>
      </div>
      <div class="settings-inline-actions">
        <button class="btn btn-secondary btn-compact" type="button">Tester l’envoi</button>
        <span class="settings-chip is-error">État de connexion : Erreur d’authentification</span>
      </div>
      <div>
        <p class="settings-preview__label">Journal des derniers tests</p>
        <ul class="settings-log">
          <li><strong>14/04/2026 09:42</strong> — Timeout SMTP sur <code>smtp.provider.com</code>.</li>
          <li><strong>14/04/2026 09:31</strong> — Authentification refusée (identifiants invalides).</li>
          <li><strong>13/04/2026 18:02</strong> — Connexion locale validée en 1.2s.</li>
        </ul>
      </div>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="api-title">
      <div class="card-header">
        <div>
          <h2 id="api-title">API &amp; IA</h2>
          <p class="settings-note">Pilote les clés, états et tests des services connectés.</p>
        </div>
        <span class="settings-badge is-active">Actif</span>
      </div>
      <div class="settings-api-grid">
        <?php foreach ([
          ['OpenAI', 'Actif', '14/04/2026 09:20'],
          ['Apify', 'Actif', '14/04/2026 09:21'],
          ['Google', 'Non configuré', '—'],
          ['Meta', 'Erreur', '14/04/2026 08:58'],
          ['Autres', 'Bientôt', '—'],
        ] as [$apiName, $apiState, $apiTestDate]): ?>
          <article class="settings-api-item">
            <div>
              <p class="settings-api-item__name"><?= htmlspecialchars($apiName) ?></p>
              <label class="form-field"><span>Clé masquée</span><input type="password" value="sk-****************" /></label>
            </div>
            <div class="settings-api-item__meta">
              <span class="settings-badge <?= $apiState === 'Actif' ? 'is-active' : ($apiState === 'Erreur' ? 'is-error' : ($apiState === 'Bientôt' ? 'is-soon' : 'is-warning')) ?>"><?= htmlspecialchars($apiState) ?></span>
              <p>Dernier test : <?= htmlspecialchars($apiTestDate) ?></p>
            </div>
            <div class="settings-inline-actions">
              <button class="btn btn-secondary btn-compact" type="button">Tester</button>
              <button class="btn btn-secondary btn-compact" type="button">Désactiver</button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="sources-title">
      <div class="card-header">
        <div>
          <h2 id="sources-title">Sources de prospection / Scrapers</h2>
          <p class="settings-note">Structure prête pour futur branchement à <code>config/prospecting.php</code> et connecteurs.</p>
        </div>
        <span class="settings-badge is-active">Actif</span>
      </div>
      <div class="settings-source-grid">
        <?php foreach ([
          ['Google Maps / Apify', 'Actif', 'API'],
          ['Google Business Profile', 'Actif', 'Scraper'],
          ['Instagram', 'Actif', 'Scraper'],
          ['Facebook', 'Non configuré', 'Scraper'],
          ['LinkedIn', 'Actif', 'Scraper'],
          ['TikTok', 'Bientôt', 'Scraper'],
          ['API officielle', 'Actif', 'API'],
          ['Source personnalisée', 'Non configuré', 'Manuel'],
        ] as [$sourceName, $sourceState, $sourceType]): ?>
          <article class="settings-source-item">
            <div class="card-header-inline">
              <h3><?= htmlspecialchars($sourceName) ?></h3>
              <span class="settings-badge <?= $sourceState === 'Actif' ? 'is-active' : ($sourceState === 'Bientôt' ? 'is-soon' : 'is-warning') ?>"><?= htmlspecialchars($sourceState) ?></span>
            </div>
            <p class="settings-note">Type : <strong><?= htmlspecialchars($sourceType) ?></strong></p>
            <div class="settings-inline-actions">
              <button class="btn btn-secondary btn-compact" type="button">Configurer</button>
              <button class="btn btn-secondary btn-compact" type="button">Tester</button>
              <button class="btn btn-secondary btn-compact" type="button">Voir autres sources/API</button>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <article class="settings-request card small">
        <h3>Demander une nouvelle intégration</h3>
        <div class="settings-grid two-cols">
          <label class="form-field"><span>Nom source</span><input type="text" placeholder="Ex: Yelp" /></label>
          <label class="form-field"><span>URL doc API</span><input type="text" placeholder="https://api.source.com/docs" /></label>
          <label class="form-field settings-grid-full"><span>Besoin métier</span><textarea placeholder="Pourquoi cette intégration est critique pour l’équipe"></textarea></label>
          <label class="form-field"><span>Priorité</span><select><option>Haute</option><option>Moyenne</option><option>Basse</option></select></label>
        </div>
      </article>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="users-title">
      <div class="card-header">
        <h2 id="users-title">Utilisateurs &amp; accès</h2>
        <span class="settings-badge is-active">Actif</span>
      </div>
      <div class="settings-grid two-cols">
        <label class="form-field"><span>Profil utilisateur</span><input type="text" value="Nina Martin" /></label>
        <label class="form-field"><span>Rôle</span><select><option>Admin</option><option>Manager</option><option>Viewer</option></select></label>
        <label class="form-field"><span>Email</span><input type="email" value="nina@acme-crm.io" /></label>
        <label class="form-field"><span>Sessions</span><input type="text" value="2 sessions actives" /></label>
        <label class="form-field settings-grid-full"><span>Permissions de module</span><input type="text" value="Prospection, Messages IA, Pipeline, Contenu" /></label>
        <label class="form-field settings-grid-full"><span>Sécurité</span><input type="text" value="MFA activée · rotation mot de passe 90 jours" /></label>
      </div>
    </section>

    <section class="card settings-card stack-md" aria-labelledby="notifications-title">
      <div class="card-header">
        <h2 id="notifications-title">Notifications &amp; automatisations</h2>
        <span class="settings-badge is-active">Actif</span>
      </div>
      <div class="settings-toggle-grid">
        <?php foreach ([
          'Notif nouveau prospect',
          'Notif erreur API',
          'Notif analyse terminée',
          'Notif import',
        ] as $notifLabel): ?>
          <label class="settings-toggle">
            <span><?= htmlspecialchars($notifLabel) ?></span>
            <input type="checkbox" checked />
          </label>
        <?php endforeach; ?>
      </div>
      <div class="settings-grid three-cols">
        <label class="form-field"><span>Canal Email</span><select><option>Activé</option><option>Désactivé</option></select></label>
        <label class="form-field"><span>Canal In-app</span><select><option>Activé</option><option>Désactivé</option></select></label>
        <label class="form-field"><span>Canal Webhook</span><select><option>Activé</option><option>Désactivé</option></select></label>
      </div>
    </section>

    <section class="card settings-card" aria-labelledby="diagnostic-title">
      <div class="card-header">
        <h2 id="diagnostic-title">Diagnostic technique</h2>
        <span class="settings-badge is-error">Erreur</span>
      </div>
      <div class="settings-diagnostic-grid">
        <p><span>Statut SMTP</span><strong>Erreur de connexion</strong></p>
        <p><span>Statut API</span><strong>3/5 services actifs</strong></p>
        <p><span>Statut scrapers</span><strong>6/8 prêts</strong></p>
        <p><span>Dernière erreur</span><strong>Meta API — token expiré</strong></p>
        <p><span>Dernier test réussi</span><strong>14/04/2026 09:21 (Apify)</strong></p>
      </div>
    </section>
  </div>
</div>
