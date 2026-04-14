(function () {
  const tabs = Array.from(document.querySelectorAll('[data-studio-tab]'));
  const panels = Array.from(document.querySelectorAll('[data-studio-panel]'));

  function activateTab(target) {
    tabs.forEach((tab) => tab.classList.toggle('is-active', tab.getAttribute('data-studio-tab') === target));
    panels.forEach((panel) => {
      const isActive = panel.getAttribute('data-studio-panel') === target;
      panel.hidden = !isActive;
      panel.classList.toggle('is-active', isActive);
    });
  }

  if (tabs.length > 0 && panels.length > 0) {
    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        activateTab(tab.getAttribute('data-studio-tab') || 'creer');
      });
    });
  }

  const form = document.getElementById('studio-generate-form');
  const frameworkInput = document.getElementById('studio-framework');
  const focusInput = document.getElementById('studio-focus-input');
  const contentType = document.getElementById('studio-content-type');
  const channel = document.getElementById('studio-channel');
  const objective = document.getElementById('studio-objective');
  const tone = document.getElementById('studio-tone');

  function applyMethodPreset(source) {
    if (!source) return;

    const framework = source.getAttribute('data-framework') || '';
    const focus = source.getAttribute('data-focus') || '';

    if (frameworkInput) frameworkInput.value = framework;
    if (focusInput && focus !== '') focusInput.value = focus;

    const typeVal = source.getAttribute('data-content-type');
    const channelVal = source.getAttribute('data-channel');
    const objectiveVal = source.getAttribute('data-objective');
    const toneVal = source.getAttribute('data-tone');

    if (contentType && typeVal) contentType.value = typeVal;
    if (channel && channelVal) channel.value = channelVal;
    if (objective && objectiveVal) objective.value = objectiveVal;
    if (tone && toneVal) tone.value = toneVal;
  }

  document.querySelectorAll('[data-use-method]').forEach((button) => {
    button.addEventListener('click', () => {
      applyMethodPreset(button);
      activateTab('creer');
      form?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      focusInput?.focus();
    });
  });

  document.querySelectorAll('[data-generate-method]').forEach((button) => {
    button.addEventListener('click', () => {
      applyMethodPreset(button);
      activateTab('creer');
      if (form) form.submit();
    });
  });

  document.querySelectorAll('[data-copy-target]').forEach((button) => {
    button.addEventListener('click', async () => {
      const targetId = button.getAttribute('data-copy-target');
      if (!targetId) return;
      const target = document.getElementById(targetId);
      if (!target) return;

      try {
        await navigator.clipboard.writeText(target.innerText.trim());
        button.textContent = 'Copié';
        setTimeout(() => {
          button.textContent = 'Copier';
        }, 1200);
      } catch (_error) {
        window.alert('Copie impossible pour le moment.');
      }
    });
  });

  const assistantInput = document.getElementById('assistant-input');
  const assistantGenerate = document.getElementById('assistant-generate');
  const assistantSave = document.getElementById('assistant-save');
  const assistantResult = document.getElementById('assistant-result');
  const assistantResultText = document.getElementById('assistant-result-text');

  assistantGenerate?.addEventListener('click', () => {
    const idea = (assistantInput?.value || '').trim();
    if (idea === '') {
      window.alert('Ajoute une idée avant de générer.');
      return;
    }

    if (assistantResult && assistantResultText) {
      assistantResult.hidden = false;
      assistantResultText.textContent = `Brouillon assistant: ${idea} — Angle conseillé: commence par la douleur principale, ajoute une preuve courte, puis termine avec un CTA simple.`;
    }
  });

  assistantSave?.addEventListener('click', () => {
    const result = (assistantResultText?.textContent || '').trim();
    if (!result) {
      window.alert('Génère d’abord un résultat à sauvegarder.');
      return;
    }

    if (focusInput) {
      focusInput.value = result;
    }

    if (frameworkInput && frameworkInput.value === '') {
      frameworkInput.value = 'Assistant IA';
    }

    activateTab('creer');
    form?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  const historySearch = document.getElementById('history-search-analysis');
  const historyType = document.getElementById('history-filter-type');
  const historyDate = document.getElementById('history-filter-date');
  const historyItems = Array.from(document.querySelectorAll('.studio-history-item'));

  function applyHistoryFilters() {
    const searchValue = (historySearch?.value || '').trim().toLowerCase();
    const typeValue = (historyType?.value || '').trim();
    const dateValue = (historyDate?.value || '').trim();

    historyItems.forEach((item) => {
      const analysis = item.getAttribute('data-analysis') || '';
      const type = item.getAttribute('data-type') || '';
      const date = item.getAttribute('data-date') || '';

      const matchesSearch = searchValue === '' || analysis.includes(searchValue);
      const matchesType = typeValue === '' || type === typeValue;
      const matchesDate = dateValue === '' || date === dateValue;

      item.hidden = !(matchesSearch && matchesType && matchesDate);
    });
  }

  [historySearch, historyType, historyDate].forEach((field) => {
    if (!field) return;
    field.addEventListener('input', applyHistoryFilters);
    field.addEventListener('change', applyHistoryFilters);
  });

  document.querySelectorAll('[data-export-item]').forEach((button) => {
    button.addEventListener('click', () => {
      const card = button.closest('.studio-history-item');
      if (!card) return;

      const content = card.innerText.trim();
      const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);
      link.href = url;
      link.download = 'studio-contenu-export.txt';
      link.click();
      URL.revokeObjectURL(url);
    });
  });
})();
