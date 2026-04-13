<?php ?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Analyse stratégique</h1>
      <p class="subtitle">
        Analyse psychologique et marketing de tes prospects pour générer contenu et messages
      </p>
    </div>

    <!-- FORM -->
    <div class="card">

      <form id="strategy-analysis-form" method="post" action="/strategie/analyse" class="stack">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

        <div class="form-group">
          <label for="profile">Profil prospect</label>
          <textarea
            id="profile"
            name="profile"
            rows="6"
            placeholder="Ex: Coach sport indépendant, 34 ans, activité irrégulière..."
            required
            class="input"
          ></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
          Analyser le prospect
        </button>
      </form>

      <p id="analysis-error" class="text-error" style="display:none;"></p>
      <p id="analysis-warning" class="text-warning" style="display:none;"></p>

    </div>

    <!-- RESULT -->
    <div id="analysis-result" class="card" style="display:none;">

      <div class="card-header">
        <h3>Résultat de l’analyse</h3>
        <span id="awareness-badge" class="badge"></span>
      </div>

      <div class="grid">

        <div class="card small">
          <h4>Résumé</h4>
          <p id="summary"></p>
        </div>

        <div class="card small">
          <h4>Pain points</h4>
          <ul id="pain-points"></ul>
        </div>

        <div class="card small">
          <h4>Désirs profonds</h4>
          <ul id="desires"></ul>
        </div>

        <div class="card small">
          <h4>Angles de contenu</h4>
          <ul id="content-angles"></ul>
        </div>

        <div class="card small">
          <h4>Hooks marketing</h4>
          <ul id="recommended-hooks"></ul>
        </div>

      </div>

      <div class="stack" style="margin-top:16px;">
        <a class="btn btn-primary" href="/contenu">
          Générer du contenu
        </a>

        <a id="create-message-cta" class="btn btn-secondary" href="/messages-ia">
          Créer message
        </a>
      </div>

    </div>

    <!-- HISTORY -->
    <div class="card">
      <div class="card-header">
        <h3>Historique des analyses</h3>
      </div>

      <?php if (($history ?? []) === []): ?>

        <div class="empty-state">
          <p class="muted">Aucune analyse sauvegardée pour le moment.</p>
        </div>

      <?php else: ?>

        <div class="stack">

          <?php foreach (($history ?? []) as $item): ?>
            <article class="card small">

              <p class="muted">
                <?= htmlspecialchars((string) $item['created_at']) ?>
                · <?= htmlspecialchars((string) ($item['awareness_level'] ?? 'N/A')) ?>
              </p>

              <p><?= htmlspecialchars((string) ($item['summary'] ?? '')) ?></p>

              <details>
                <summary>Voir le profil</summary>
                <pre><?= htmlspecialchars((string) ($item['profile_text'] ?? '')) ?></pre>
              </details>

            </article>
          <?php endforeach; ?>

        </div>

      <?php endif; ?>
    </div>

  </div>
</div>

<script>
/* JS inchangé */
</script>
