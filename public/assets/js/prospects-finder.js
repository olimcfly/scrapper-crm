(function () {
  const root = document.querySelector('[data-finder-root]');
  if (!root) return;

  const ui = {
    loading: root.querySelector('[data-loading-state]'),
    list: root.querySelector('[data-prospect-list]'),
    emptyState: root.querySelector('[data-empty-state]'),
    activeFilters: root.querySelector('[data-active-filters]'),
    chips: Array.from(root.querySelectorAll('[data-category-chip]')),
    searchInput: root.querySelector('[data-search-input]'),
    categoryInput: root.querySelector('[data-category-input]'),
    openSheetBtn: document.querySelector('[data-open-sheet]'),
    sheet: document.querySelector('[data-filter-sheet]'),
    sheetBackdrop: document.querySelector('[data-sheet-backdrop]'),
    closeSheetBtn: document.querySelector('[data-close-sheet]'),
    resetFiltersBtn: document.querySelector('[data-reset-filters]'),
    sheetCategory: document.querySelector('[data-sheet-category]'),
    sheetCategorySelect: document.querySelector('[data-sheet-category-select]'),
    sheetCity: document.querySelector('[data-sheet-city]'),
    sheetAwareness: document.querySelector('[data-sheet-awareness]'),
    sheetWebsite: document.querySelector('[data-sheet-website]'),
    sheetSocial: document.querySelector('[data-sheet-social]'),
    sheetPriority: document.querySelector('[data-sheet-priority]'),
    sheetStatus: document.querySelector('[data-sheet-status]'),
    sheetZone: document.querySelector('[data-sheet-zone]'),
  };

  const cards = Array.from(root.querySelectorAll('[data-card]'));
  const urlParams = new URLSearchParams(window.location.search);

  const mockProspects = [
    {
      search: 'Sophie Martin coach bien-être lyon',
      category: 'Coach bien-être',
      city: 'Lyon',
      awareness: 'Conscient du besoin',
      website: 'oui',
      social: 'oui',
      priority: 'moyen',
      status: '0',
      zone: 'Locale',
    },
    {
      search: 'Espace Harmonie spa nantes',
      category: 'Praticiennes SPA',
      city: 'Nantes',
      awareness: 'À éduquer',
      website: 'non',
      social: 'oui',
      priority: 'faible',
      status: '0',
      zone: 'Locale',
    },
  ];

  const state = {
    loading: true,
    sheetOpen: false,
    query: (urlParams.get('q') || '').trim(),
    category: (urlParams.get('category') || 'Tous').trim(),
    filters: {
      city: (urlParams.get('city') || '').trim(),
      awareness: (urlParams.get('awareness_level') || '').trim(),
      website: (urlParams.get('website_presence') || '').trim(),
      social: (urlParams.get('social_presence') || '').trim(),
      priority: (urlParams.get('priority') || '').trim(),
      status: (urlParams.get('status_id') || '').trim(),
      zone: (urlParams.get('zone_scope') || '').trim(),
    },
  };

  const normalize = (value) => (value || '').toString().toLowerCase().trim();
  const contains = (source, needle) => normalize(source).includes(normalize(needle));

  const toRecord = (card) => ({
    node: card,
    search: normalize(card.dataset.search || card.textContent || ''),
    category: card.dataset.category || '',
    city: card.dataset.city || '',
    awareness: card.dataset.awareness || '',
    website: card.dataset.website || '',
    social: card.dataset.social || '',
    priority: card.dataset.priority || '',
    status: card.dataset.status || '',
    zone: card.dataset.zone || '',
  });

  const prospects = cards.length > 0 ? cards.map(toRecord) : mockProspects.map((m) => ({ ...m, node: null }));

  const activeFilterEntries = () => {
    const entries = [];
    if (state.query) entries.push(['Recherche', state.query]);
    if (state.category && state.category !== 'Tous') entries.push(['Catégorie', state.category]);

    const map = [
      ['Ville', state.filters.city],
      ['Conscience', state.filters.awareness],
      ['Site web', state.filters.website],
      ['Réseaux', state.filters.social],
      ['Priorité', state.filters.priority],
      ['Statut CRM', state.filters.status && state.filters.status !== '0' ? state.filters.status : ''],
      ['Zone', state.filters.zone],
    ];

    map.forEach(([label, value]) => {
      if (value) entries.push([label, value]);
    });

    return entries;
  };

  const toggleSheet = (open) => {
    state.sheetOpen = open;
    document.body.classList.toggle('bottom-sheet-open', open);
    if (ui.sheet) ui.sheet.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (ui.sheetBackdrop) ui.sheetBackdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
  };

  const renderActiveFilters = () => {
    if (!ui.activeFilters) return;

    const entries = activeFilterEntries();
    if (entries.length === 0) {
      ui.activeFilters.innerHTML = '';
      return;
    }

    const pills = entries
      .map(([label, value]) => `<span class="active-filter-pill">${label}: ${value}</span>`)
      .join('');

    ui.activeFilters.innerHTML = `${pills}<button type="button" class="active-filter-clear" data-clear-active-filters>Réinitialiser tout</button>`;

    const clearBtn = ui.activeFilters.querySelector('[data-clear-active-filters]');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        state.query = '';
        state.category = 'Tous';
        state.filters = { city: '', awareness: '', website: '', social: '', priority: '', status: '', zone: '' };
        syncInputsFromState();
        refreshList();
      });
    }
  };

  const filterRecord = (record) => {
    const byQuery = !state.query || contains(record.search, state.query) || contains(record.city, state.query);
    const byCategory = state.category === 'Tous' || record.category === state.category;
    const byCity = !state.filters.city || contains(record.city, state.filters.city);
    const byAwareness = !state.filters.awareness || record.awareness === state.filters.awareness;
    const byWebsite = !state.filters.website || record.website === state.filters.website;
    const bySocial = !state.filters.social || record.social === state.filters.social;
    const byPriority = !state.filters.priority || record.priority === state.filters.priority;
    const byStatus = !state.filters.status || state.filters.status === '0' || record.status === state.filters.status;
    const byZone = !state.filters.zone || record.zone === state.filters.zone;

    return byQuery && byCategory && byCity && byAwareness && byWebsite && bySocial && byPriority && byStatus && byZone;
  };

  const renderCards = () => {
    let visible = 0;

    prospects.forEach((record) => {
      const match = filterRecord(record);
      if (record.node) {
        record.node.style.display = match ? 'grid' : 'none';
      }
      if (match) visible += 1;
    });

    if (ui.list) ui.list.style.display = visible > 0 ? 'grid' : 'none';
    if (ui.emptyState) ui.emptyState.style.display = visible > 0 ? 'none' : 'block';
  };

  const syncChips = () => {
    ui.chips.forEach((chip) => {
      const active = chip.dataset.categoryChip === state.category;
      chip.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    if (ui.categoryInput) ui.categoryInput.value = state.category;
    if (ui.sheetCategory) ui.sheetCategory.value = state.category;
    if (ui.sheetCategorySelect) ui.sheetCategorySelect.value = state.category;
  };

  const syncInputsFromState = () => {
    if (ui.searchInput) ui.searchInput.value = state.query;
    if (ui.sheetCity) ui.sheetCity.value = state.filters.city;
    if (ui.sheetAwareness) ui.sheetAwareness.value = state.filters.awareness;
    if (ui.sheetWebsite) ui.sheetWebsite.value = state.filters.website;
    if (ui.sheetSocial) ui.sheetSocial.value = state.filters.social;
    if (ui.sheetPriority) ui.sheetPriority.value = state.filters.priority;
    if (ui.sheetStatus) ui.sheetStatus.value = state.filters.status || '0';
    if (ui.sheetZone) ui.sheetZone.value = state.filters.zone;
    syncChips();
  };

  const setLoading = (loading) => {
    state.loading = loading;
    if (ui.loading) ui.loading.style.display = loading ? 'block' : 'none';
    if (loading && ui.list) ui.list.style.display = 'none';
  };

  const refreshList = () => {
    setLoading(true);
    window.setTimeout(() => {
      renderCards();
      renderActiveFilters();
      setLoading(false);
    }, 140);
  };

  const applySheetFilters = () => {
    state.filters.city = (ui.sheetCity?.value || '').trim();
    state.filters.awareness = (ui.sheetAwareness?.value || '').trim();
    state.filters.website = (ui.sheetWebsite?.value || '').trim();
    state.filters.social = (ui.sheetSocial?.value || '').trim();
    state.filters.priority = (ui.sheetPriority?.value || '').trim();
    state.filters.status = (ui.sheetStatus?.value || '').trim();
    state.filters.zone = (ui.sheetZone?.value || '').trim();
    state.category = (ui.sheetCategorySelect?.value || state.category || 'Tous').trim();

    toggleSheet(false);
    syncInputsFromState();
    refreshList();
  };

  const bindEvents = () => {
    if (ui.searchInput) {
      let timeout = null;
      ui.searchInput.addEventListener('input', (event) => {
        window.clearTimeout(timeout);
        timeout = window.setTimeout(() => {
          state.query = (event.target.value || '').trim();
          refreshList();
        }, 120);
      });
    }

    ui.chips.forEach((chip) => {
      chip.addEventListener('click', () => {
        state.category = chip.dataset.categoryChip || 'Tous';
        syncChips();
        refreshList();
      });
    });

    if (ui.openSheetBtn) ui.openSheetBtn.addEventListener('click', () => toggleSheet(true));
    if (ui.closeSheetBtn) ui.closeSheetBtn.addEventListener('click', () => toggleSheet(false));
    if (ui.sheetBackdrop) ui.sheetBackdrop.addEventListener('click', () => toggleSheet(false));

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && state.sheetOpen) toggleSheet(false);
    });

    if (ui.sheet) {
      ui.sheet.addEventListener('submit', (event) => {
        event.preventDefault();
        applySheetFilters();
      });
    }

    if (ui.resetFiltersBtn) {
      ui.resetFiltersBtn.addEventListener('click', () => {
        state.filters = { city: '', awareness: '', website: '', social: '', priority: '', status: '', zone: '' };
        state.category = 'Tous';
        syncInputsFromState();
        refreshList();
      });
    }

    root.addEventListener('click', (event) => {
      const action = event.target.closest('[data-quick-action]');
      if (!action) return;
      const card = event.target.closest('[data-card]');
      if (!card) return;
      card.dataset.state = 'selected';
      window.setTimeout(() => {
        if (card.dataset.state === 'selected') card.dataset.state = 'default';
      }, 450);
    });
  };

  syncInputsFromState();
  bindEvents();
  refreshList();
})();
