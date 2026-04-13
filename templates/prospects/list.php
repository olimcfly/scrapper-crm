<div class="page">
  <div class="container">

    <!-- HEADER GLOBAL -->
    <div class="page-header">
      <h1>Prospection intelligente</h1>
      <p class="subtitle">Trouvez, analysez et convertissez vos prospects bien-être</p>
    </div>

    <!-- FINDER -->
    <section class="prospects-finder" data-finder-root>

      <!-- HEADER FINDER -->
      <form method="get" action="/prospects" class="finder-header">

        <input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>" data-category-input>

        <div class="finder-toolbar-head">
          <div class="finder-title-row">
            <h2>Trouver des praticiennes</h2>
            <p class="finder-kpi"><?= $totalProspects ?> résultat(s)</p>
          </div>

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
          <div class="finder-kpi">Base ciblée de professionnelles du bien-être</div>

          <div class="finder-actions">
            <button type="button" class="btn btn-secondary" data-open-sheet>
              Filtres<?= count($cleanFilters) > 0 ? ' (' . count($cleanFilters) . ')' : '' ?>
            </button>

            <a class="btn btn-primary" href="/prospects/create">
              + Nouveau prospect
            </a>
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

        <div class="empty-state">
          <p>Aucun prospect trouvé</p>
          <a class="btn btn-primary" href="/prospects/create">Créer un prospect</a>
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