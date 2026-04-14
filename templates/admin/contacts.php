<?php
$contacts = is_array($contacts ?? null) ? $contacts : [];
$filters = is_array($filters ?? null) ? $filters : [];
$stats = is_array($stats ?? null) ? $stats : ['total' => 0, 'new' => 0, 'contacted' => 0, 'clients' => 0];
$sources = is_array($sources ?? null) ? $sources : [];
$statuses = is_array($statuses ?? null) ? $statuses : [];
$pagination = is_array($pagination ?? null) ? $pagination : ['page' => 1, 'total_pages' => 1];

$statusClass = static function (?string $status): string {
    $normalized = mb_strtolower(trim((string) $status));
    if (str_contains($normalized, 'client')) {
        return 'is-client';
    }
    if (str_contains($normalized, 'contact')) {
        return 'is-contacted';
    }
    if (str_contains($normalized, 'new') || str_contains($normalized, 'nouveau') || str_contains($normalized, 'lead')) {
        return 'is-new';
    }

    return 'is-other';
};

$dataQuality = static function (array $contact): array {
    $score = 0;
    foreach (['professional_email', 'professional_phone', 'website', 'linkedin_url', 'city'] as $field) {
        if (trim((string) ($contact[$field] ?? '')) !== '') {
            $score += 20;
        }
    }

    if ($score >= 80) {
        return ['label' => 'Excellente', 'class' => 'q-high'];
    }
    if ($score >= 60) {
        return ['label' => 'Bonne', 'class' => 'q-medium'];
    }

    return ['label' => 'À enrichir', 'class' => 'q-low'];
};
?>

<div class="page contacts-page">
  <div class="container contacts-center">
    <header class="contacts-header card">
      <div>
        <p class="eyebrow">Centre de conversion</p>
        <h1>Contacts</h1>
        <p class="subtitle">Tous vos prospects et clients dans une seule vue actionnable.</p>
      </div>
      <div class="contacts-header-actions">
        <a class="btn btn-primary" href="/prospects/sources">Trouver des contacts</a>
        <a class="btn btn-secondary" href="/prospects/import">Importer</a>
      </div>
    </header>

    <section class="contacts-stats-grid">
      <article class="card stat-card"><span>Total</span><strong><?= (int) ($stats['total'] ?? 0) ?></strong></article>
      <article class="card stat-card"><span>Nouveaux</span><strong><?= (int) ($stats['new'] ?? 0) ?></strong></article>
      <article class="card stat-card"><span>Contactés</span><strong><?= (int) ($stats['contacted'] ?? 0) ?></strong></article>
      <article class="card stat-card"><span>Clients</span><strong><?= (int) ($stats['clients'] ?? 0) ?></strong></article>
    </section>

    <form class="card contacts-filters" method="get" action="/admin/modules/contacts">
      <div class="filters-grid">
        <input class="input" type="search" name="q" placeholder="Recherche texte" value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>">

        <select class="input" name="source_id">
          <option value="">Source</option>
          <?php foreach ($sources as $source): ?>
            <?php $selected = ((string) ($filters['source_id'] ?? '')) === (string) $source['id']; ?>
            <option value="<?= (int) $source['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= htmlspecialchars((string) $source['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <select class="input" name="status_id">
          <option value="">Statut</option>
          <?php foreach ($statuses as $status): ?>
            <?php $selected = ((string) ($filters['status_id'] ?? '')) === (string) $status['id']; ?>
            <option value="<?= (int) $status['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= htmlspecialchars((string) $status['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <input class="input" type="text" name="city" placeholder="Ville" value="<?= htmlspecialchars((string) ($filters['city'] ?? '')) ?>">

        <label class="checkbox-inline"><input type="checkbox" name="has_email" value="1" <?= !empty($filters['has_email']) ? 'checked' : '' ?>>Email dispo</label>
        <label class="checkbox-inline"><input type="checkbox" name="has_phone" value="1" <?= !empty($filters['has_phone']) ? 'checked' : '' ?>>Téléphone dispo</label>
      </div>
      <div class="filters-actions">
        <button class="btn btn-primary" type="submit">Appliquer</button>
        <a class="btn btn-secondary" href="/admin/modules/contacts">Réinitialiser</a>
      </div>
    </form>

    <section class="card contacts-table-card">
      <div class="table-wrapper">
        <table class="table contacts-table">
          <thead>
          <tr>
            <th>Nom / business</th><th>Source</th><th>Statut</th><th>Ville</th><th>Email</th><th>Téléphone</th><th>Date ajout</th><th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($contacts as $contact): ?>
            <?php
            $quality = $dataQuality($contact);
            $contactId = (int) $contact['id'];
            $detailId = 'contact-detail-' . $contactId;
            $potential = max(0, min(100, (int) ($contact['score'] ?? 0)));
            $statusName = (string) ($contact['status_name'] ?? 'À qualifier');
            ?>
            <tr class="contact-row" data-detail-target="<?= htmlspecialchars($detailId) ?>">
              <td>
                <strong><?= htmlspecialchars((string) (($contact['full_name'] ?? '') !== '' ? $contact['full_name'] : trim((string) (($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''))))) ?></strong>
                <div class="muted"><?= htmlspecialchars((string) ($contact['business_name'] ?? '—')) ?></div>
              </td>
              <td><?= htmlspecialchars((string) ($contact['source_name'] ?? '—')) ?></td>
              <td><span class="status-chip <?= $statusClass($statusName) ?>"><?= htmlspecialchars($statusName) ?></span></td>
              <td><?= htmlspecialchars((string) ($contact['city'] ?? '—')) ?></td>
              <td><?= htmlspecialchars((string) ($contact['professional_email'] ?? '—')) ?></td>
              <td><?= htmlspecialchars((string) ($contact['professional_phone'] ?? '—')) ?></td>
              <td><?= htmlspecialchars(substr((string) ($contact['created_at'] ?? ''), 0, 10) ?: '—') ?></td>
              <td>
                <div class="row-actions">
                  <button type="button" title="Ajouter au CRM">➕</button>
                  <button type="button" title="Envoyer email">✉️</button>
                  <button type="button" title="Générer message IA">🤖</button>
                  <button type="button" title="Analyser">🔎</button>
                  <button type="button" title="Ajouter à campagne">📣</button>
                  <button type="button" title="Marquer important">⭐</button>
                  <button type="button" title="Supprimer">🗑️</button>
                </div>
              </td>
            </tr>
            <tr id="<?= htmlspecialchars($detailId) ?>" class="contact-detail-row" hidden>
              <td colspan="8">
                <div class="contact-detail-panel">
                  <div><strong>Qualité des données :</strong> <span class="quality-pill <?= htmlspecialchars($quality['class']) ?>"><?= htmlspecialchars($quality['label']) ?></span></div>
                  <div><strong>Campagnes associées :</strong> <?= htmlspecialchars((string) ($contact['campaigns'] ?? 'Aucune campagne')) ?></div>
                  <div><strong>Score potentiel :</strong> <span class="potential-score"><?= $potential ?>/100</span></div>
                  <div><strong>Prochaine action :</strong> <?= htmlspecialchars((string) ($contact['prochaine_action'] ?? 'À définir')) ?></div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ($contacts === []): ?>
            <tr><td colspan="8" class="muted">Aucun contact trouvé avec ces filtres.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ((int) ($pagination['page'] ?? 1) < (int) ($pagination['total_pages'] ?? 1)): ?>
        <?php $nextQuery = $filters; $nextQuery['page'] = (int) ($pagination['page'] ?? 1) + 1; ?>
        <div class="contacts-pagination">
          <a class="btn btn-secondary" href="/admin/modules/contacts?<?= htmlspecialchars(http_build_query($nextQuery)) ?>">Voir plus</a>
        </div>
      <?php endif; ?>
    </section>
  </div>
</div>

<script src="/assets/js/contacts-center.js" defer></script>
