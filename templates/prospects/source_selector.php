<?php

function safe_array($value) {
  return is_array($value) ? $value : [];
}

function safe_string($value) {
  return htmlspecialchars((string) ($value ?? ''));
}

$sourcesData = safe_array($sources ?? null);
$accountsData = safe_array($connectedAccounts ?? null);
$runsData = safe_array($searchRuns ?? null);

?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Trouver des prospects</h1>
      <p class="subtitle">
        Choisissez une source et lancez votre collecte
      </p>
    </div>

    <!-- CONFIG -->
    <div class="card">

      <div class="card-header">
        <h3>Configuration de la recherche</h3>
      </div>

      <div class="stack">

        <div class="form-group">
          <label for="source">Source</label>
          <select id="source" class="input"></select>
        </div>

        <div class="form-group">
          <label for="search_type">Type de recherche</label>
          <select id="search_type" class="input"></select>
        </div>

        <div id="dynamic-fields" class="stack"></div>

        <div class="row">
          <button id="test-connection" class="btn btn-secondary" type="button">
            Tester connexion
          </button>

          <button id="run-search" class="btn btn-primary" type="button">
            Lancer la collecte
          </button>
        </div>

        <p id="run-feedback" class="muted"></p>

      </div>

    </div>

    <!-- GRID -->
    <div class="grid">

      <!-- STATUT -->
      <div class="card">

        <div class="card-header">
          <h3>Statut des sources de collecte</h3>
        </div>

        <?php if (empty($accountsData)): ?>

          <div class="empty-state">
            <p class="muted">Aucune connexion enregistrée</p>
          </div>

        <?php else: ?>

          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr>
                  <th>Source</th>
                  <th>Statut</th>
                  <th>Erreur</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($accountsData as $account): ?>
                  <tr>
                    <td><?= safe_string($account['source'] ?? '') ?></td>
                    <td><?= safe_string($account['status'] ?? '') ?></td>
                    <td><?= safe_string($account['error_message'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        <?php endif; ?>

      </div>

      <!-- RUNS -->
      <div class="card">

        <div class="card-header">
          <h3>Historique des collectes</h3>
        </div>

        <?php if (empty($runsData)): ?>

          <div class="empty-state">
            <p class="muted">Aucune recherche lancée</p>
          </div>

        <?php else: ?>

          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr>
                  <th>Source</th>
                  <th>Type</th>
                  <th>Statut</th>
                  <th>Résultats</th>
                  <th>Erreur</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($runsData as $run): ?>
                  <tr>
                    <td><?= safe_string($run['source'] ?? '') ?></td>
                    <td><?= safe_string($run['search_type'] ?? '') ?></td>
                    <td><?= safe_string($run['status'] ?? '') ?></td>
                    <td><?= safe_string($run['results_count'] ?? '0') ?></td>
                    <td><?= safe_string($run['error_message'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        <?php endif; ?>

      </div>

    </div>

  </div>
</div>

<script>
(() => {
  const sources = <?= json_encode($sourcesData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const sourceSelect = document.getElementById('source');
  const typeSelect = document.getElementById('search_type');
  const fieldsWrap = document.getElementById('dynamic-fields');
  const feedback = document.getElementById('run-feedback');

  const sourceKeys = Object.keys(sources);

  sourceKeys.forEach((key) => {
    const option = document.createElement('option');
    option.value = key;
    option.textContent = `${sources[key].label} (${sources[key].kind})`;
    sourceSelect.appendChild(option);
  });

  const render = () => {
    const source = sourceSelect.value;
    const config = sources[source] || {};

    typeSelect.innerHTML = '';

    (config.search_types || []).forEach((type) => {
      const option = document.createElement('option');
      option.value = type;
      option.textContent = type;
      typeSelect.appendChild(option);
    });

    fieldsWrap.innerHTML = '';

    (config.fields || []).forEach((field) => {
      const box = document.createElement('div');
      box.className = 'form-group';

      const label = document.createElement('label');
      label.textContent = field.label;

      const input = document.createElement('input');
      input.name = field.name;
      input.className = 'input';

      box.appendChild(label);
      box.appendChild(input);
      fieldsWrap.appendChild(box);
    });
  };

  const payload = () => {
    const filters = {};
    fieldsWrap.querySelectorAll('input').forEach((input) => {
      if (input.value !== '') filters[input.name] = input.value;
    });

    return {
      source: sourceSelect.value,
      search_type: typeSelect.value,
      credentials: filters,
      filters,
    };
  };

  document.getElementById('test-connection').addEventListener('click', async () => {
    const res = await fetch('/api/prospecting/connect/test', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload()),
    });
    const json = await res.json();
    feedback.textContent = json.error || json.data?.message || 'Connexion testée';
  });

  document.getElementById('run-search').addEventListener('click', async () => {
    const res = await fetch('/api/prospecting/search', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload()),
    });
    const json = await res.json();
    const statut = json.data?.run_status === 'success' ? 'réussie' : (json.data?.run_status === 'failed' ? 'échouée' : 'en cours');
    feedback.textContent = json.error || `Collecte ${statut} • ${json.data?.results_count ?? 0} résultats`;
  });

  if (sourceKeys.length > 0) {
    sourceSelect.value = sourceKeys[0];
    render();
  }

  sourceSelect.addEventListener('change', render);
})();
</script>
