<?php
$prospects = is_array($prospects ?? null) ? $prospects : [];
$filters = is_array($filters ?? null) ? $filters : [];
$statuses = is_array($statuses ?? null) ? $statuses : [];
$sources = is_array($sources ?? null) ? $sources : [];
$pagination = is_array($pagination ?? null) ? $pagination : [];

$cleanFilters = is_array($cleanFilters ?? null)
    ? $cleanFilters
    : array_values(array_filter($filters, static fn ($value): bool => $value !== '' && $value !== 0 && $value !== '0'));

$activeCategory = is_string($activeCategory ?? null) && $activeCategory !== ''
    ? $activeCategory
    : (is_string($filters['category'] ?? null) && $filters['category'] !== '' ? $filters['category'] : 'Tous');

$categoryOrder = is_array($categoryOrder ?? null) && $categoryOrder !== []
    ? $categoryOrder
    : ['Tous'];

$totalProspects = isset($totalProspects) ? (int) $totalProspects : (int) ($pagination['total'] ?? count($prospects));
$currentPage = isset($currentPage) ? (int) $currentPage : (int) ($pagination['page'] ?? 1);
$totalPages = isset($totalPages) ? (int) $totalPages : (int) ($pagination['pages'] ?? 1);
$query = is_array($query ?? null) ? $query : $filters;
?>

<div class="page">
  <div class="container">

    <!-- HEADER GLOBAL -->
    <div class="page-header">
      <h1>Prospection</h1>
      <p class="subtitle">Trouvez, analysez et convertissez vos prospects</p>
    </div>

    <!-- FINDER -->
    <section class="prospects-finder" data-finder-root>

      <!-- HEADER FINDER -->
      <form method="get" action="/prospects" class="finder-header">

        <input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>" data-category-input>

        <div class="finder-toolbar-head">
          <div class="finder-title-row">
            <h2>Trouver des prospects</h2>
            <p class="finder-kpi"><?= $totalProspects ?> résultat(s)</p>
          </div>

          <p class="finder-subtitle">Choisissez une source et lancez votre collecte</p>

          <div class="finder-search">
            <input
              type="search"
              name="q"
              placeholder="Nom, spécialité, ville..."
              value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>"
              autocomplete="off"
              data-search-input
            >
          </div>
        </div>

        <div class="finder-primary-cta">
          <a class="btn btn-primary finder-main-cta" href="/prospects/sources">
            Lancer une collecte
          </a>
        </div>

        <div class="finder-secondary-cta">
          <a class="btn btn-secondary finder-secondary-btn" href="/prospects/create">
            Ajouter manuellement
          </a>
          <a class="btn btn-secondary finder-secondary-btn" href="/prospects/import">
            Importer un fichier
          </a>
        </div>

        <div class="finder-source-grid" aria-label="Sources de collecte suggérées">
          <?php foreach (['Google Maps', 'LinkedIn', 'Instagram', 'Facebook', 'TikTok', 'Google Business Profile'] as $sourceLabel): ?>
            <article class="source-card">
              <strong><?= htmlspecialchars($sourceLabel) ?></strong>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="category-scroll">
          <?php foreach ($categoryOrder as $category): ?>
            <?php $isActive = $category === $activeCategory; ?>
            <button
              type="button"
              class="category-chip"
              data-category-chip="<?= htmlspecialchars($category) ?>"
              aria-pressed="<?= $isActive ? 'true' : 'false' ?>"
            >
              <?= htmlspecialchars($category) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="finder-toolbar">
          <div class="finder-kpi">Base ciblée de prospects à qualifier</div>

          <div class="finder-actions">
            <button type="button" class="btn btn-secondary" data-open-sheet>
              Filtres<?= count($cleanFilters) > 0 ? ' (' . count($cleanFilters) . ')' : '' ?>
            </button>
          </div>
        </div>

      </form>

      <!-- STATES -->
      <?php if (!empty($successMessage)): ?>
        <div class="global-state success">
          <span class="state-dot"></span>
          <div><?= htmlspecialchars((string) $successMessage) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($warningMessage)): ?>
        <div class="global-state warning">
          <span class="state-dot"></span>
          <div><?= htmlspecialchars((string) $warningMessage) ?></div>
        </div>
      <?php endif; ?>

      <div class="active-filters" data-active-filters></div>

      <!-- LOADING -->
      <div class="finder-loading" data-loading-state>
        <p>Chargement des prospects...</p>
        <div class="finder-loading-grid">
          <div class="finder-skeleton"></div>
          <div class="finder-skeleton"></div>
          <div class="finder-skeleton"></div>
        </div>
      </div>

      <!-- EMPTY -->
      <?php if (empty($prospects)): ?>

        <div data-empty-state>
          <?php
            $emptyTitle = 'Aucun prospect trouvé';
            $emptyDescription = 'Lancez une collecte ou ajustez vos filtres pour faire apparaître des opportunités.';
            $emptyCtaHref = '/prospects/sources';
            $emptyCtaLabel = 'Lancer une collecte';
            require __DIR__ . '/../components/states/empty_state_guided.php';
          ?>
        </div>

      <?php else: ?>

        <!-- LIST -->
        <div class="prospect-list" data-prospect-list style="display:none;">
          <?php foreach ($prospects as $prospect): ?>
            <?php
              $prospectCard = $prospect;
              $cardState = 'default';
              require __DIR__ . '/../components/prospect_card.php';
            ?>
          <?php endforeach; ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
          <div class="finder-pagination">

            <?php if ($currentPage > 1): ?>
              <?php $query['page'] = (string) ($currentPage - 1); ?>
              <a class="btn btn-secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">
                ← Précédent
              </a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
              <?php $query['page'] = (string) ($currentPage + 1); ?>
              <a class="btn btn-secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">
                Suivant →
              </a>
            <?php endif; ?>

          </div>
        <?php endif; ?>

      <?php endif; ?>

    </section>

  </div>
</div>

<?php
require __DIR__ . '/../components/prospect_filters_bottom_sheet.php';
?>

<script src="/assets/js/prospects-finder.js" defer></script>
