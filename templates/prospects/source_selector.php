<?php

function safe_array($value) {
  return is_array($value) ? $value : [];
}

function safe_string($value) {
  return htmlspecialchars((string) ($value ?? ''));
}

function readable_source(string $source): string {
  return match ($source) {
    'google_maps_scraper' => 'Google Maps',
    'instagram' => 'Instagram',
    default => ucwords(str_replace('_', ' ', $source)),
  };
}

function readable_type(string $type): string {
  return match ($type) {
    'keyword' => 'Mot-clé',
    'city' => 'Ville',
    'hashtag' => 'Hashtag',
    default => ucfirst($type),
  };
}

function readable_status(string $status): string {
  return match ($status) {
    'success' => 'Réussi',
    'failed' => 'Échec',
    default => ucfirst($status),
  };
}

function extraction_parameter(array $run): string {
  $filters = json_decode((string) ($run['filters_json'] ?? '{}'), true);
  if (!is_array($filters)) {
    $filters = [];
  }

  foreach (['city', 'keyword', 'hashtag', 'query', 'location'] as $key) {
    $value = trim((string) ($filters[$key] ?? ''));
    if ($value !== '') {
      return $value;
    }
  }

  return 'Non renseigné';
}

$sourcesData = safe_array($sources ?? null);
$accountsData = safe_array($connectedAccounts ?? null);
$runsData = safe_array($searchRuns ?? null);

?>

<div class="page">
  <div class="container prospects-collections">

    <div class="page-header">
      <h1>Trouver des prospects</h1>
      <p class="subtitle">Lancez une collecte puis passez immédiatement à l’action commerciale.</p>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Nouvelle collecte</h3>
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

        <div class="row collection-cta-row">
          <button id="test-connection" class="btn btn-secondary" type="button">Tester la connexion</button>
          <button id="run-search" class="btn btn-primary" type="button">Lancer la collecte</button>
        </div>

        <p id="run-feedback" class="muted"></p>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>État des connexions</h3>
      </div>

      <?php if (empty($accountsData)): ?>
        <div class="empty-state">
          <p class="muted">Aucune connexion enregistrée.</p>
        </div>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th>Source</th>
                <th>Statut</th>
                <th>Détail</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($accountsData as $account): ?>
                <tr>
                  <td><?= safe_string(readable_source((string) ($account['source'] ?? ''))) ?></td>
                  <td><?= safe_string((string) ($account['status'] ?? '')) ?></td>
                  <td><?= safe_string((string) ($account['error_message'] ?? 'Connectée')) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <section class="collection-history" aria-label="Historique des collectes">
      <div class="card-header">
        <h3>Historique des collectes</h3>
      </div>

      <?php if (empty($runsData)): ?>
        <div class="card empty-state">
          <p class="muted">Aucune collecte pour le moment.</p>
        </div>
      <?php else: ?>
        <div class="collection-cards">
          <?php foreach ($runsData as $run): ?>
            <article class="card collection-card">
              <a href="/prospects/collectes/<?= (int) ($run['id'] ?? 0) ?>" class="collection-main-link">
                <div class="collection-head">
                  <p class="collection-source"><?= safe_string(readable_source((string) ($run['source'] ?? ''))) ?></p>
                  <span class="status-pill"><?= safe_string(readable_status((string) ($run['status'] ?? ''))) ?></span>
                </div>

                <div class="collection-grid">
                  <p><strong>Type :</strong> <?= safe_string(readable_type((string) ($run['search_type'] ?? ''))) ?></p>
                  <p><strong>Paramètre :</strong> <?= safe_string(extraction_parameter($run)) ?></p>
                  <p><strong>Prospects trouvés :</strong> <?= (int) ($run['results_count'] ?? 0) ?></p>
                </div>
              </a>

              <div class="collection-actions">
                <a class="btn btn-secondary" href="/prospects/collectes/<?= (int) ($run['id'] ?? 0) ?>">Voir les prospects</a>
                <a class="btn btn-secondary" href="/strategie">Lancer une analyse</a>
                <a class="btn btn-primary" href="/prospects/import">Importer dans le CRM</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

  </div>
</div>

<script>
(() => {
  const sources = <?= json_encode($sourcesData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const sourceSelect = document.getElementById('source');
  const typeSelect = document.getElementById('search_type');
  const fieldsWrap = document.getElementById('dynamic-fields');
  const feedback = document.getElementById('run-feedback');

  const typeLabels = {
    keyword: 'Mot-clé',
    city: 'Ville',
    hashtag: 'Hashtag',
  };

  const sourceKeys = Object.keys(sources);

  sourceKeys.forEach((key) => {
    const option = document.createElement('option');
    option.value = key;
    option.textContent = `${sources[key].label}`;
    sourceSelect.appendChild(option);
  });

  const render = () => {
    const source = sourceSelect.value;
    const config = sources[source] || {};

    typeSelect.innerHTML = '';

    (config.search_types || []).forEach((type) => {
      const option = document.createElement('option');
      option.value = type;
      option.textContent = typeLabels[type] || type;
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
    const statut = json.data?.run_status === 'success' ? 'Réussi' : (json.data?.run_status === 'failed' ? 'Échec' : 'En cours');
    feedback.textContent = json.error || `Collecte ${statut} • ${json.data?.results_count ?? 0} prospects`;
  });

  if (sourceKeys.length > 0) {
    sourceSelect.value = sourceKeys[0];
    render();
  }

  sourceSelect.addEventListener('change', render);
})();
</script>
