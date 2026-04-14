<?php

function safe_string($value): string {
  return htmlspecialchars((string) ($value ?? ''));
}

function run_label(string $value, string $kind): string {
  if ($kind === 'source') {
    return match ($value) {
      'google_maps_scraper' => 'Google Maps',
      'instagram' => 'Instagram',
      default => ucwords(str_replace('_', ' ', $value)),
    };
  }

  if ($kind === 'status') {
    return match ($value) {
      'success' => 'Réussi',
      'failed' => 'Échec',
      default => ucfirst($value),
    };
  }

  return match ($value) {
    'keyword' => 'Mot-clé',
    'city' => 'Ville',
    'hashtag' => 'Hashtag',
    default => ucfirst($value),
  };
}

$runData = is_array($run ?? null) ? $run : [];
$items = is_array($prospects ?? null) ? $prospects : [];
?>

<div class="page">
  <div class="container prospects-collections">
    <div class="page-header">
      <h1>Détail de la collecte</h1>
      <p class="subtitle">Accédez directement aux leads générés par cette collecte.</p>
    </div>

    <div class="card collection-detail-header">
      <p><strong>Source :</strong> <?= safe_string(run_label((string) ($runData['source'] ?? ''), 'source')) ?></p>
      <p><strong>Type :</strong> <?= safe_string(run_label((string) ($runData['search_type'] ?? ''), 'type')) ?></p>
      <p><strong>Statut :</strong> <?= safe_string(run_label((string) ($runData['status'] ?? ''), 'status')) ?></p>
      <p><strong>Prospects trouvés :</strong> <?= (int) ($runData['results_count'] ?? 0) ?></p>
    </div>

    <?php if ($items === []): ?>
      <div class="card empty-state">
        <p class="muted">Aucun prospect disponible pour cette collecte.</p>
      </div>
    <?php else: ?>
      <section class="collection-cards" aria-label="Liste prospects collectés">
        <?php foreach ($items as $prospect): ?>
          <article class="card collection-card">
            <div class="collection-grid">
              <p><strong>Nom :</strong> <?= safe_string((string) ($prospect['name'] ?? '')) ?></p>
              <p><strong>Téléphone :</strong> <?= safe_string((string) ($prospect['phone'] ?? '')) ?></p>
              <p><strong>Email :</strong> <?= safe_string((string) ($prospect['email'] ?? '')) ?></p>
              <p><strong>Ville :</strong> <?= safe_string((string) ($prospect['city'] ?? '')) ?></p>
              <?php if (isset($prospect['score']) && $prospect['score'] !== null && $prospect['score'] !== ''): ?>
                <p><strong>Score :</strong> <?= safe_string((string) $prospect['score']) ?></p>
              <?php endif; ?>
            </div>

            <div class="collection-actions">
              <a class="btn btn-primary" href="/prospects/create?nom=<?= urlencode((string) ($prospect['name'] ?? '')) ?>&telephone=<?= urlencode((string) ($prospect['phone'] ?? '')) ?>&email=<?= urlencode((string) ($prospect['email'] ?? '')) ?>&ville=<?= urlencode((string) ($prospect['city'] ?? '')) ?>&score=<?= urlencode((string) ($prospect['score'] ?? '')) ?>">Ajouter au CRM</a>
              <a class="btn btn-secondary" href="/messages-ia">Générer message</a>
              <a class="btn btn-secondary" href="/strategie">Analyser</a>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </div>
</div>
