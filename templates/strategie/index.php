<?php
$history = is_array($history ?? null) ? $history : [];
?>

<div class="page strategy-page">
  <div class="container">

    <div class="page-header">
      <h1>Stratégie orientée action</h1>
      <p class="subtitle">Décris ton marché en 4 champs, l’IA structure ton angle et tes prochaines actions.</p>
    </div>

    <div class="card strategy-form-card">
      <div class="card-header">
        <h3>Brief guidé</h3>
      </div>

      <form id="strategy-analysis-form" method="post" action="/strategie/analyse" class="stack">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

        <div class="strategy-form-grid">
          <div class="form-group">
            <label for="business_type">Type de métier</label>
            <select id="business_type" name="business_type" class="input" required>
              <option value="">Choisir un métier</option>
              <option value="Coach">Coach</option>
              <option value="Thérapeute">Thérapeute</option>
              <option value="Consultant">Consultant</option>
              <option value="Agence">Agence</option>
              <option value="Freelance">Freelance</option>
              <option value="Autre">Autre</option>
            </select>
          </div>

          <div class="form-group">
            <label for="city">Ville</label>
            <input id="city" name="city" class="input" placeholder="Ex : Lyon" required>
          </div>

          <div class="form-group form-field-full">
            <label for="target">Cible principale</label>
            <input id="target" name="target" class="input" placeholder="Ex : femmes actives 30-45 ans, stressées" required>
          </div>

          <div class="form-group form-field-full">
            <label for="pain_point">Problématique principale</label>
            <textarea id="pain_point" name="pain_point" class="input" rows="4" placeholder="Ex : elles hésitent longtemps avant de réserver un premier RDV" required></textarea>
          </div>
        </div>

        <div class="strategy-highlight" id="generated-prompt-preview">
          Le prompt IA structuré apparaîtra ici pendant que tu complètes le brief.
        </div>

        <button type="submit" class="btn btn-primary">Analyser et générer des actions</button>
      </form>

      <p id="analysis-error" class="text-error" style="display:none;"></p>
      <p id="analysis-warning" class="text-warning" style="display:none;"></p>
    </div>

    <div id="analysis-result" class="card strategy-analysis-card" style="display:none;">
      <div class="card-header">
        <h3>Lecture IA du marché</h3>
        <span id="awareness-badge" class="badge"></span>
      </div>

      <section class="card small ai-suggestion-block">
        <p class="ai-suggestion-title">Suggestion IA</p>
        <p id="ai-human-message">Je te propose de commencer par une relance empathique puis d’enchaîner sur une preuve sociale locale.</p>
      </section>

      <div class="grid">
        <div class="card small"><h4>Résumé</h4><p id="summary"></p></div>
        <div class="card small"><h4>Pain points</h4><ul id="pain-points"></ul></div>
        <div class="card small"><h4>Désirs profonds</h4><ul id="desires"></ul></div>
        <div class="card small"><h4>Angles de contenu</h4><ul id="content-angles"></ul></div>
        <div class="card small"><h4>Hooks marketing</h4><ul id="recommended-hooks"></ul></div>
      </div>

      <div class="stack" style="margin-top:16px;">
        <a class="btn btn-primary" href="/contenu">Générer du contenu</a>
        <a id="create-message-cta" class="btn btn-secondary" href="/messages-ia">Créer un message</a>
      </div>
    </div>

    <div class="card strategy-history-card">
      <div class="card-header"><h3>Historique des analyses</h3></div>

      <?php if ($history === []): ?>
        <?php
          $emptyTitle = 'Aucune stratégie enregistrée';
          $emptyDescription = 'Crée ton premier brief guidé pour obtenir des actions prêtes à exécuter.';
          $emptyCtaHref = '/strategie';
          $emptyCtaLabel = 'Démarrer un brief';
          require __DIR__ . '/../components/states/empty_state_guided.php';
        ?>
      <?php else: ?>
        <div class="stack">
          <?php foreach ($history as $item): ?>
            <article class="card small">
              <p class="muted"><?= htmlspecialchars((string) $item['created_at']) ?> · <?= htmlspecialchars((string) ($item['awareness_level'] ?? 'N/A')) ?></p>
              <p><?= htmlspecialchars((string) ($item['summary'] ?? '')) ?></p>
              <details>
                <summary>Voir le brief source</summary>
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
(() => {
  const form = document.querySelector('#strategy-analysis-form');
  if (!form) return;

  const fields = {
    businessType: form.querySelector('#business_type'),
    city: form.querySelector('#city'),
    target: form.querySelector('#target'),
    painPoint: form.querySelector('#pain_point'),
  };

  const preview = document.querySelector('#generated-prompt-preview');
  const errorEl = document.querySelector('#analysis-error');
  const warningEl = document.querySelector('#analysis-warning');
  const result = document.querySelector('#analysis-result');
  const awareness = document.querySelector('#awareness-badge');
  const aiHumanMessage = document.querySelector('#ai-human-message');

  const listMap = {
    'pain-points': 'pain_points',
    'desires': 'desires',
    'content-angles': 'content_angles',
    'recommended-hooks': 'recommended_hooks',
  };

  const buildPrompt = () => {
    const payload = {
      business_type: fields.businessType.value.trim(),
      city: fields.city.value.trim(),
      target: fields.target.value.trim(),
      pain_point: fields.painPoint.value.trim(),
    };

    return `Tu es un stratège commercial orienté action.\nMétier: ${payload.business_type || '[à définir]'}\nVille: ${payload.city || '[à définir]'}\nCible: ${payload.target || '[à définir]'}\nProblématique: ${payload.pain_point || '[à définir]'}\n\nRetourne: niveau de conscience, résumé, pain points, désirs, angles de contenu, hooks.`;
  };

  const refreshPromptPreview = () => {
    if (!preview) return;
    preview.textContent = buildPrompt();
  };

  Object.values(fields).forEach((field) => field?.addEventListener('input', refreshPromptPreview));
  refreshPromptPreview();

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    errorEl.style.display = 'none';
    warningEl.style.display = 'none';

    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
      });

      const payload = await response.json();

      if (!response.ok || !payload.success) {
        errorEl.textContent = payload.error || 'Analyse impossible, réessaie dans quelques instants.';
        errorEl.style.display = 'block';
        return;
      }

      if (payload.meta?.warning) {
        warningEl.textContent = payload.meta.warning;
        warningEl.style.display = 'block';
      }

      const data = payload.data || {};
      result.style.display = 'block';
      awareness.textContent = data.awareness_level || 'N/A';
      document.querySelector('#summary').textContent = data.summary || '';

      Object.entries(listMap).forEach(([id, key]) => {
        const node = document.querySelector(`#${id}`);
        if (!node) return;
        node.innerHTML = '';
        (Array.isArray(data[key]) ? data[key] : []).forEach((item) => {
          const li = document.createElement('li');
          li.textContent = item;
          node.appendChild(li);
        });
      });

      aiHumanMessage.textContent = `Je te conseille de prioriser la cible "${fields.target.value.trim()}" à ${fields.city.value.trim()} avec une relance centrée sur le problème exprimé.`;
      document.querySelector('#create-message-cta')?.setAttribute('href', '/messages-ia?type=relance');
      result.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (error) {
      errorEl.textContent = 'Erreur réseau. Vérifie ta connexion puis réessaie.';
      errorEl.style.display = 'block';
    }
  });
})();
</script>
