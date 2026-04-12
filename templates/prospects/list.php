<?php
$categoryOrder = [
    'Tous',
    'Agents immobiliers',
    'Commerçants',
    'Thérapeutes',
    'Coachs',
    'Artisans',
    'Indépendants',
    'Restaurants',
    'Autres',
];

$activeCategory = trim((string) ($filters['category'] ?? 'Tous'));
if ($activeCategory === '' || !in_array($activeCategory, $categoryOrder, true)) {
    $activeCategory = 'Tous';
}

$normalize = static fn (string $value): string => mb_strtolower(trim($value));
$detectCategory = static function (array $prospect) use ($normalize): string {
    $activity = $normalize((string) ($prospect['activity'] ?? ''));
    $business = $normalize((string) ($prospect['business_name'] ?? ''));
    $haystack = $activity . ' ' . $business;

    if ($haystack === '') {
        return 'Autres';
    }

    $mapping = [
        'Agents immobiliers' => ['agent immobilier', 'immobilier', 'mandataire'],
        'Commerçants' => ['commerce', 'boutique', 'magasin', 'retail', 'e-commerce'],
        'Thérapeutes' => ['thérapeute', 'therapeute', 'psychologue', 'naturopathe', 'sophrologue'],
        'Coachs' => ['coach', 'coaching', 'mentor'],
        'Artisans' => ['artisan', 'plombier', 'électricien', 'electricien', 'menuisier', 'coiffeur'],
        'Indépendants' => ['freelance', 'indépendant', 'independant', 'consultant', 'solopreneur'],
        'Restaurants' => ['restaurant', 'restauration', 'traiteur', 'café', 'cafe', 'bar'],
    ];

    foreach ($mapping as $label => $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return $label;
            }
        }
    }

    return 'Autres';
};

$statusLabel = '';
$statusFilterId = (int) ($filters['status_id'] ?? 0);
if ($statusFilterId > 0) {
    foreach (($statuses ?? []) as $statusItem) {
        if ((int) ($statusItem['id'] ?? 0) === $statusFilterId) {
            $statusLabel = (string) ($statusItem['name'] ?? '');
            break;
        }
    }
}

$activeAdvancedFilters = [
    'Ville' => trim((string) ($filters['city'] ?? '')),
    'Niveau conscience' => trim((string) ($filters['awareness_level'] ?? '')),
    'Réseaux sociaux' => trim((string) ($filters['social_presence'] ?? '')),
    'Site web' => trim((string) ($filters['website_presence'] ?? '')),
    'Priorité' => trim((string) ($filters['priority'] ?? '')),
    'Statut CRM' => $statusLabel,
    'Activité' => trim((string) ($filters['zone_scope'] ?? '')),
];

$cleanFilters = array_filter($activeAdvancedFilters, static fn (string $value): bool => $value !== '');
$totalProspects = (int) ($pagination['total'] ?? 0);
?>

<style>
  .prospects-finder {
    --finder-bg: #f4f6fb;
    --finder-border: #e4e8f2;
    --finder-border-strong: #cfd7ea;
    --finder-text-muted: #667085;
    --finder-text-strong: #0f172a;
    --finder-indigo: #3f4bff;
    --finder-indigo-dark: #2e39d8;
    --finder-surface: #ffffff;
    --finder-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
    --finder-shadow-soft: 0 8px 24px rgba(15, 23, 42, 0.06);
    display: grid;
    gap: 16px;
  }

  .finder-header {
    position: sticky;
    top: 10px;
    z-index: 12;
    border-radius: 20px;
    border: 1px solid var(--finder-border);
    background:
      radial-gradient(140% 120% at 10% -20%, rgba(63, 75, 255, 0.12) 0%, rgba(63, 75, 255, 0) 45%),
      rgba(255, 255, 255, 0.94);
    box-shadow: var(--finder-shadow);
    backdrop-filter: blur(10px);
    padding: 14px;
    display: grid;
    gap: 12px;
    grid-template-areas:
      "title"
      "search"
      "category"
      "actions";
  }

  .finder-title-row { grid-area: title; }
  .finder-search { grid-area: search; }
  .category-scroll { grid-area: category; }
  .finder-toolbar { grid-area: actions; }

  .finder-title-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
  }

  .finder-title-row h2 {
    margin: 0;
    font-size: 21px;
    font-weight: 700;
    letter-spacing: -0.02em;
  }

  .finder-kpi {
    margin: 0;
    color: var(--finder-text-muted);
    font-size: 13px;
  }

  .finder-search {
    position: relative;
  }

  .finder-search input {
    width: 100%;
    border-radius: 14px;
    border: 1px solid var(--finder-border);
    background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
    padding: 12px 14px 12px 38px;
    font-size: 15px;
    transition: border-color .2s ease, box-shadow .2s ease;
  }

  .finder-search input:focus-visible {
    outline: none;
    border-color: #98a6ff;
    box-shadow: 0 0 0 4px rgba(63, 75, 255, 0.14);
  }

  .finder-search svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.45;
  }

  .category-scroll {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
    scrollbar-color: #cfd8ec transparent;
  }

  .category-chip {
    border: 1px solid var(--finder-border);
    background: linear-gradient(180deg, #fff 0%, #f8faff 100%);
    color: #0f172a;
    border-radius: 999px;
    padding: 8px 13px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all .2s ease;
  }

  .category-chip:hover,
  .category-chip:focus-visible {
    border-color: #aab8ff;
    outline: none;
    transform: translateY(-1px);
  }

  .category-chip[aria-pressed="true"] {
    background: var(--finder-indigo);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 22px rgba(71, 85, 255, 0.32);
  }

  .finder-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
  }

  .finder-btn {
    border: none;
    border-radius: 12px;
    background: #111827;
    color: #fff;
    padding: 11px 14px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background .2s ease, transform .15s ease;
  }

  .finder-btn:hover,
  .finder-btn:focus-visible {
    background: #1f2937;
    outline: none;
  }

  .finder-btn:active {
    transform: scale(0.98);
  }

  .finder-btn.secondary {
    background: var(--finder-indigo);
  }

  .finder-btn.secondary:hover,
  .finder-btn.secondary:focus-visible {
    background: var(--finder-indigo-dark);
  }

  .active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
  }

  .active-filter-pill {
    border-radius: 999px;
    border: 1px solid #d8def7;
    padding: 7px 11px;
    font-size: 12px;
    color: #334155;
    background: #f8faff;
  }

  .active-filter-clear {
    margin-left: auto;
    font-size: 12px;
    color: #475569;
    text-decoration: none;
    border: none;
    background: transparent;
    cursor: pointer;
  }

  .prospect-list {
    display: grid;
    gap: 12px;
  }

  .prospect-card {
    background: linear-gradient(180deg, #ffffff 0%, #fcfdff 100%);
    border: 1px solid var(--finder-border);
    border-radius: 18px;
    padding: 15px;
    box-shadow: var(--finder-shadow-soft);
    display: grid;
    gap: 12px;
    transition: transform .18s ease, box-shadow .22s ease, border-color .2s ease;
  }

  .prospect-card:hover {
    transform: translateY(-2px);
    border-color: var(--finder-border-strong);
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
  }

  .prospect-card:focus-within {
    border-color: #93a7ff;
    box-shadow: 0 0 0 3px rgba(71, 85, 255, 0.16);
  }

  .prospect-card[data-state='selected'] {
    border-color: #7c8cff;
    box-shadow: 0 10px 28px rgba(71, 85, 255, 0.2);
  }

  .prospect-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
  }

  .prospect-name {
    margin: 0;
    font-size: 16px;
    line-height: 1.2;
    letter-spacing: -0.01em;
  }

  .prospect-meta {
    margin: 5px 0 0;
    color: var(--finder-text-muted);
    font-size: 13px;
  }

  .status-pill,
  .score-pill,
  .awareness-pill,
  .priority-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 999px;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .crm-status-pill {
    background: #eef2ff;
    color: #3730a3;
    border-color: #c7d2fe;
  }

  .score-pill { background: #eef3ff; color: #1e40af; border-color: #c8d6ff; }
  .ia-opportunity-pill { background: #ecfeff; color: #0f766e; border-color: #99f6e4; }
  .awareness-hot { background: #edfdf4; color: #047857; border-color: #a7f3d0; }
  .awareness-warm { background: #fff7ed; color: #b45309; border-color: #fdba74; }
  .awareness-cold { background: #f4f6fa; color: #475569; border-color: #d8dee8; }
  .priority-high { background: #fef2f2; color: #b91c1c; border-color: #fca5a5; }
  .priority-medium { background: #eef4ff; color: #1d4ed8; border-color: #bfdbfe; }
  .priority-low { background: #f6f3ff; color: #6d28d9; border-color: #ddd6fe; }

  .prospect-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .presence-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    font-size: 12px;
    color: #334155;
  }

  .presence-item {
    background: #f8fafc;
    border-radius: 11px;
    padding: 7px 9px;
    border: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    gap: 6px;
  }

  .presence-ok { color: #16a34a; font-weight: 700; }
  .presence-off { color: #94a3b8; font-weight: 700; }
  .presence-neutral { color: #334155; font-weight: 700; }

  .ai-assist-block {
    border: 1px solid #dbe5ff;
    background: linear-gradient(180deg, #f8faff 0%, #f2f6ff 100%);
    border-radius: 12px;
    padding: 10px;
    display: grid;
    gap: 6px;
  }

  .ai-assist-kicker {
    margin: 0;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #334155;
  }

  .ai-assist-line {
    margin: 0;
    font-size: 12px;
    color: #1e293b;
  }

  .ai-assist-preview {
    margin: 2px 0 0;
    font-size: 12px;
    color: #475569;
    border-top: 1px dashed #cbd5e1;
    padding-top: 6px;
  }

  .quick-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
  }

  .quick-action {
    border-radius: 11px;
    border: 1px solid #d7def0;
    background: linear-gradient(180deg, #fff 0%, #fafbff 100%);
    color: #0f172a;
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    transition: all .18s ease;
  }

  .quick-action:hover,
  .quick-action:focus-visible {
    border-color: #95aafc;
    color: #2932bf;
    outline: none;
  }

  .quick-action:active { transform: scale(0.98); }
  .quick-action.ia { background: #101828; color: #fff; border-color: #101828; box-shadow: 0 8px 18px rgba(16, 24, 40, 0.18); }

  .finder-empty,
  .finder-loading {
    border-radius: 18px;
    border: 1px solid var(--finder-border);
    background: #fff;
    padding: 18px;
  }

  .finder-empty h3 { margin: 0 0 6px; }
  .finder-empty p,
  .finder-loading p { margin: 0; color: var(--finder-text-muted); }

  .finder-loading-grid {
    margin-top: 12px;
    display: grid;
    gap: 10px;
  }

  .finder-skeleton {
    height: 90px;
    border-radius: 14px;
    background: linear-gradient(90deg, #ebf1ff 25%, #f8fbff 50%, #ebf1ff 75%);
    background-size: 220% 100%;
    animation: finder-shimmer 1.4s infinite;
  }

  .finder-pagination {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 2px;
  }

  .bottom-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(2, 6, 23, 0.45);
    z-index: 50;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
  }

  .bottom-sheet {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 55;
    border-radius: 22px 22px 0 0;
    background: #fff;
    padding: 18px 16px 16px;
    max-height: 84vh;
    overflow-y: auto;
    transform: translateY(104%);
    transition: transform .25s ease;
    box-shadow: 0 -18px 46px rgba(15, 23, 42, 0.26);
  }

  .bottom-sheet-open .bottom-sheet-backdrop {
    opacity: 1;
    pointer-events: auto;
  }

  .bottom-sheet-open .bottom-sheet {
    transform: translateY(0);
  }

  .sheet-handle {
    width: 44px;
    height: 5px;
    background: #cbd5e1;
    border-radius: 999px;
    margin: 0 auto 10px;
  }

  .sheet-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
  }

  .sheet-close-btn {
    border: 1px solid #d8e0f4;
    background: linear-gradient(180deg, #fff 0%, #f8faff 100%);
    color: #334155;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
  }

  .sheet-close-btn:active {
    transform: scale(0.96);
  }

  .sheet-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .sheet-grid label {
    display: grid;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
  }

  .sheet-grid label.full { grid-column: 1 / -1; }
  .sheet-actions {
    position: sticky;
    bottom: -16px;
    margin: 12px -16px -16px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    padding: 12px 16px;
    display: flex;
    gap: 8px;
    box-shadow: 0 -8px 22px rgba(15, 23, 42, 0.06);
  }

  @keyframes finder-shimmer {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
  }

  @media (min-width: 700px) {
    .prospects-finder { gap: 18px; }
    .finder-header {
      padding: 18px;
      grid-template-columns: minmax(0, 1fr) auto;
      grid-template-areas:
        "title actions"
        "search search"
        "category category";
      align-items: center;
    }
    .finder-title-row h2 { font-size: 22px; }
    .finder-search input { font-size: 15px; padding-top: 13px; padding-bottom: 13px; }
    .finder-toolbar { justify-content: flex-end; gap: 10px; }
    .active-filters { gap: 10px; }
    .prospect-list { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .prospect-card { padding: 16px; }
    .quick-actions { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .bottom-sheet { left: 50%; right: auto; width: min(740px, calc(100vw - 32px)); transform: translate(-50%, 104%); }
    .bottom-sheet-open .bottom-sheet { transform: translate(-50%, 0); }
  }

  @media (min-width: 1060px) {
    .prospects-finder { gap: 20px; }
    .finder-header {
      grid-template-columns: minmax(220px, 0.9fr) minmax(280px, 1.1fr) auto;
      grid-template-areas:
        "title search actions"
        "category category category";
      gap: 14px;
    }
    .finder-title-row { align-items: baseline; }
    .finder-title-row h2 { font-size: 24px; }
    .finder-kpi { font-size: 12px; }
    .category-scroll { padding-bottom: 2px; }
    .prospect-list { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .prospect-card { gap: 13px; }
    .presence-grid { gap: 10px; }
    .quick-actions { grid-template-columns: repeat(5, minmax(0, 1fr)); }
  }

  @media (min-width: 1280px) {
    .finder-header {
      grid-template-columns: minmax(240px, 0.9fr) minmax(380px, 1.2fr) auto;
    }
    .prospect-list { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .bottom-sheet {
      left: auto;
      right: 24px;
      width: 430px;
      border-radius: 18px;
      bottom: 24px;
      max-height: calc(100vh - 48px);
      transform: translateY(120%);
    }
    .bottom-sheet-open .bottom-sheet { transform: translateY(0); }
    .sheet-grid { grid-template-columns: 1fr; }
    .sheet-grid label.full { grid-column: auto; }
  }
</style>

<section class="prospects-finder" data-finder-root>
  <form method="get" action="/prospects" class="finder-header">
    <input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>" data-category-input>
    <div class="finder-title-row">
      <h2>Trouver des prospects</h2>
      <p class="finder-kpi"><?= $totalProspects ?> résultat(s)</p>
    </div>

    <div class="finder-search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 21l-4.3-4.3m1.3-5.2a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
      <input
        type="search"
        name="q"
        placeholder="Nom, activité, ville, email..."
        value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>"
        autocomplete="off"
        data-search-input
      >
    </div>

    <div class="category-scroll" role="tablist" aria-label="Catégories de prospects">
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
      <a class="btn secondary compact" href="/prospects/create">+ Nouveau prospect</a>
      <button type="button" class="finder-btn" data-open-sheet>
        Filtres avancés<?= count($cleanFilters) > 0 ? ' • ' . count($cleanFilters) : '' ?>
      </button>
    </div>
  </form>

  <?php if (!empty($successMessage)): ?>
    <div class="global-state loading"><span class="state-dot" aria-hidden="true"></span><div><?= htmlspecialchars((string) $successMessage) ?></div></div>
  <?php endif; ?>

  <?php if (!empty($warningMessage)): ?>
    <div class="global-state error"><span class="state-dot" aria-hidden="true"></span><div><?= htmlspecialchars((string) $warningMessage) ?></div></div>
  <?php endif; ?>

  <div class="active-filters" data-active-filters></div>

  <div class="finder-loading" data-loading-state>
    <p>Chargement intelligent des prospects...</p>
    <div class="finder-loading-grid">
      <div class="finder-skeleton"></div>
      <div class="finder-skeleton"></div>
      <div class="finder-skeleton"></div>
    </div>
  </div>

  <?php if (empty($prospects)): ?>
    <article class="finder-empty" data-empty-state>
      <h3>Aucun prospect correspondant</h3>
      <p>Changez de catégorie ou allégez les filtres avancés pour élargir la recherche.</p>
      <p style="margin-top:10px;"><a class="btn" href="/prospects/create">Ajouter un prospect</a></p>
    </article>
  <?php else: ?>
    <div class="prospect-list" data-prospect-list style="display:none;">
      <?php foreach ($prospects as $prospect): ?>
        <?php
          $prospectCard = $prospect;
          $cardState = 'default';
          require __DIR__ . '/../components/prospect_card.php';
        ?>
      <?php endforeach; ?>
    </div>

    <article class="finder-empty" data-empty-state style="display:none;">
      <h3>Aucun prospect après filtrage</h3>
      <p>Ajustez les filtres actifs, puis relancez la recherche pour retrouver des opportunités.</p>
    </article>

    <?php
      $currentPage = (int) ($pagination['page'] ?? 1);
      $totalPages = (int) ($pagination['total_pages'] ?? 1);
      $query = [
        'q' => (string) ($filters['q'] ?? ''),
        'status_id' => (string) ((int) ($filters['status_id'] ?? 0)),
        'source_id' => (string) ((int) ($filters['source_id'] ?? 0)),
        'category' => $activeCategory,
      ];
    ?>

    <?php if ($totalPages > 1): ?>
      <div class="finder-pagination">
        <?php if ($currentPage > 1): ?>
          <?php $query['page'] = (string) ($currentPage - 1); ?>
          <a class="btn secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">← Précédent</a>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages): ?>
          <?php $query['page'] = (string) ($currentPage + 1); ?>
          <a class="btn secondary" href="/prospects?<?= htmlspecialchars(http_build_query($query)) ?>">Suivant →</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>

<?php
  $sheetFilters = $filters ?? [];
  $sheetStatuses = $statuses ?? [];
  $sheetSources = $sources ?? [];
  $sheetCategoryOrder = $categoryOrder;
  $sheetActiveCategory = $activeCategory;
  require __DIR__ . '/../components/prospect_filters_bottom_sheet.php';
?>
<script src="/assets/js/prospects-finder.js" defer></script>
