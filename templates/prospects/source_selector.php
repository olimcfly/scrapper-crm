<div class="card page-lead stack-sm">
  <p class="eyebrow">Trouver des prospects</p>
  <h2>Architecture multi-sources</h2>
  <p>Sépare les connecteurs de prospection (search) des APIs officielles de comptes (connect/sync).</p>
</div>

<div class="card stack-md">
  <h3>Choisir la source</h3>
  <div class="row">
    <div>
      <label for="source">Source</label>
      <select id="source"></select>
    </div>
    <div>
      <label for="search_type">Type de recherche</label>
      <select id="search_type"></select>
    </div>
  </div>

  <div id="dynamic-fields" class="row"></div>

  <div class="row">
    <button id="test-connection" class="btn secondary" type="button">Tester connexion</button>
    <button id="run-search" class="btn" type="button">Lancer recherche</button>
  </div>
  <p id="run-feedback" class="muted"></p>
</div>

<div class="row">
  <div class="card" style="flex:2; min-width: 360px;">
    <h3>Statut de connexion</h3>
    <table>
      <thead><tr><th>Source</th><th>Statut</th><th>Erreur</th></tr></thead>
      <tbody>
      <?php foreach (($connectedAccounts ?? []) as $account): ?>
        <tr>
          <td><?= htmlspecialchars((string) ($account['source'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($account['status'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($account['error_message'] ?? '')) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card" style="flex:3; min-width: 420px;">
    <h3>Runs récents</h3>
    <table>
      <thead><tr><th>Source</th><th>Type</th><th>Statut run</th><th>Résultats</th><th>Erreur</th></tr></thead>
      <tbody>
      <?php foreach (($searchRuns ?? []) as $run): ?>
        <tr>
          <td><?= htmlspecialchars((string) ($run['source'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($run['search_type'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($run['status'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string) ($run['results_count'] ?? '0')) ?></td>
          <td><?= htmlspecialchars((string) ($run['error_message'] ?? '')) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
(() => {
  const sources = <?= json_encode($sources ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
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
      const label = document.createElement('label');
      label.setAttribute('for', `fld_${field.name}`);
      label.textContent = field.label;

      const input = document.createElement('input');
      input.id = `fld_${field.name}`;
      input.name = field.name;
      input.type = field.type || 'text';
      if (field.required) input.required = true;

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
    feedback.textContent = json.error || `Run ${json.data?.run_status} • ${json.data?.results_count ?? 0} résultats`;
  });

  if (sourceKeys.length > 0) {
    sourceSelect.value = sourceKeys[0];
    render();
  }

  sourceSelect.addEventListener('change', render);
})();
</script>
