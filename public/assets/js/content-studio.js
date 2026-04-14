(function () {
  const tabs = Array.from(document.querySelectorAll('[data-studio-tab]'));
  const panels = Array.from(document.querySelectorAll('[data-studio-panel]'));

  if (tabs.length > 0 && panels.length > 0) {
    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.getAttribute('data-studio-tab');

        tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
        panels.forEach((panel) => {
          const isActive = panel.getAttribute('data-studio-panel') === target;
          panel.hidden = !isActive;
          panel.classList.toggle('is-active', isActive);
        });
      });
    });
  }

  document.querySelectorAll('[data-expand-id]').forEach((button) => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-expand-id');
      if (!id) return;
      const target = document.getElementById(id);
      if (!target) return;

      const nowHidden = !target.hidden;
      target.hidden = nowHidden;
      button.textContent = nowHidden ? 'Développer' : 'Réduire';
    });
  });

  document.querySelectorAll('[data-copy-text]').forEach((button) => {
    button.addEventListener('click', async () => {
      const content = button.getAttribute('data-copy-text') || '';
      if (content === '') return;

      try {
        await navigator.clipboard.writeText(content);
        button.textContent = 'Copié';
        setTimeout(() => {
          button.textContent = 'Copier';
        }, 1200);
      } catch (error) {
        window.alert('Copie impossible pour le moment.');
      }
    });
  });

  document.querySelectorAll('[data-copy-section]').forEach((button) => {
    button.addEventListener('click', async () => {
      const id = button.getAttribute('data-copy-section');
      if (!id) return;

      const section = document.getElementById(id);
      if (!section) return;

      try {
        await navigator.clipboard.writeText(section.innerText.trim());
        button.textContent = 'Copié';
        setTimeout(() => {
          button.textContent = 'Copier';
        }, 1200);
      } catch (error) {
        window.alert('Copie impossible pour le moment.');
      }
    });
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

  document.querySelectorAll('[data-local-delete]').forEach((button) => {
    button.addEventListener('click', () => {
      const card = button.closest('.studio-history-item');
      if (!card) return;
      card.remove();
    });
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
