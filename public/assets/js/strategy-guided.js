(() => {
  const form = document.getElementById('strategy-analysis-form');
  if (!form) return;

  const catalog = window.STRATEGY_CATALOG || {};
  const steps = Array.from(form.querySelectorAll('.strategy-step'));
  const stepper = document.getElementById('strategy-stepper');
  const synthesis = document.getElementById('strategy-synthesis');
  const recommendationText = document.getElementById('strategy-recommendation-text');
  const quickModeField = document.getElementById('quick_mode');

  const prevBtn = document.getElementById('strategy-prev');
  const nextBtn = document.getElementById('strategy-next');
  const submitBtn = document.getElementById('strategy-submit');
  const quickBtn = document.getElementById('strategy-quick-mode');

  const errorNode = document.getElementById('analysis-error');
  const warningNode = document.getElementById('analysis-warning');
  const resultCard = document.getElementById('analysis-result');

  const state = { step: 1, quickMode: false };

  const labels = {
    objective: 'Objectif',
    persona_group: 'Cible',
    persona_subtype: 'Sous-catégorie',
    offer_type: 'Offre',
    maturity_level: 'Niveau du prospect',
    contact_intention: 'Intention de contact',
  };

  const readLabel = (name, value) => {
    if (!value) return 'À préciser';
    const map = {
      objective: catalog.objectives || {},
      persona_group: catalog.persona_groups || {},
      offer_type: catalog.offer_types || {},
      maturity_level: catalog.maturity_levels || {},
      contact_intention: catalog.intentions || {},
    };

    if (name === 'persona_subtype') {
      const group = form.elements.persona_group.value;
      const sub = (catalog.persona_subtypes || {})[group] || {};
      return sub[value] || value;
    }

    const item = map[name]?.[value];
    if (!item) return value;
    return typeof item === 'string' ? item : item.label;
  };

  const fieldsByStep = {
    1: ['objective'],
    2: ['persona_group'],
    3: ['offer_type'],
    4: ['maturity_level', 'contact_intention'],
  };

  const createStepper = () => {
    stepper.innerHTML = steps.map((_, idx) => `
      <button type="button" class="strategy-step-dot" data-goto="${idx + 1}">
        <span>Étape ${idx + 1}</span>
      </button>
    `).join('');

    stepper.querySelectorAll('[data-goto]').forEach((button) => {
      button.addEventListener('click', () => {
        const target = Number(button.getAttribute('data-goto'));
        if (target <= state.step || validateStep(state.step)) {
          state.step = target;
          render();
        }
      });
    });
  };

  const setError = (message = '') => {
    errorNode.textContent = message;
    errorNode.hidden = message === '';
  };

  const setWarning = (message = '') => {
    warningNode.textContent = message;
    warningNode.hidden = message === '';
  };

  const validateStep = (step) => {
    const required = fieldsByStep[step] || [];
    if (state.quickMode && (step === 3 || step === 4)) return true;

    for (const name of required) {
      if (!form.elements[name].value) {
        setError('Merci de sélectionner une option avant de continuer.');
        return false;
      }
    }

    setError('');
    return true;
  };

  const applyPersonaDefaults = () => {
    const persona = form.elements.persona_group.value;
    const defaults = (catalog.defaults_by_persona || {})[persona] || null;
    if (!defaults) return;

    if (!form.elements.objective.value && defaults.objective) {
      form.elements.objective.value = defaults.objective;
      syncOptionState('objective', defaults.objective);
    }

    if (!form.elements.offer_type.value && defaults.offer_type) {
      form.elements.offer_type.value = defaults.offer_type;
      syncOptionState('offer_type', defaults.offer_type);
    }

    if (!form.elements.maturity_level.value && defaults.maturity_level) {
      form.elements.maturity_level.value = defaults.maturity_level;
      syncOptionState('maturity_level', defaults.maturity_level);
    }

    if (!form.elements.contact_intention.value && defaults.intention) {
      form.elements.contact_intention.value = defaults.intention;
      syncOptionState('contact_intention', defaults.intention);
    }
  };

  const updateSubtypes = () => {
    const wrap = document.getElementById('persona-subtype-wrap');
    const select = document.getElementById('persona_subtype');
    const persona = form.elements.persona_group.value;
    const options = (catalog.persona_subtypes || {})[persona] || null;

    if (!options) {
      wrap.hidden = true;
      select.innerHTML = '<option value="">Choisir</option>';
      select.value = '';
      return;
    }

    wrap.hidden = false;
    select.innerHTML = '<option value="">Choisir</option>'
      + Object.entries(options).map(([key, label]) => `<option value="${key}">${label}</option>`).join('');
  };

  const refreshSynthesis = () => {
    const entries = Object.keys(labels).map((name) => {
      const value = form.elements[name]?.value || '';
      return `<div><span>${labels[name]}</span><strong>${readLabel(name, value)}</strong></div>`;
    }).join('');

    synthesis.innerHTML = entries;

    const objective = readLabel('objective', form.elements.objective.value).toLowerCase();
    const target = readLabel('persona_group', form.elements.persona_group.value).toLowerCase();
    const subtype = readLabel('persona_subtype', form.elements.persona_subtype.value);
    const offer = readLabel('offer_type', form.elements.offer_type.value).toLowerCase();

    if (!form.elements.objective.value || !form.elements.persona_group.value) {
      recommendationText.textContent = 'Sélectionne au moins un objectif et une cible pour obtenir une recommandation dynamique.';
      return;
    }

    const targetLabel = subtype && subtype !== 'À préciser' ? `${target} (${subtype.toLowerCase()})` : target;
    recommendationText.textContent = `Tu cherches à ${objective} auprès de ${targetLabel}. L’approche la plus pertinente semble orientée ${offer}, avec un message progressif et contextualisé.`;
  };

  const syncOptionState = (name, value) => {
    document.querySelectorAll(`[data-name="${name}"] .strategy-option`).forEach((node) => {
      node.classList.toggle('is-selected', node.getAttribute('data-value') === value);
    });
    refreshSynthesis();
  };

  const render = () => {
    steps.forEach((node, idx) => {
      node.classList.toggle('is-active', idx + 1 === state.step);
    });

    stepper.querySelectorAll('[data-goto]').forEach((dot, idx) => {
      dot.classList.toggle('is-active', idx + 1 === state.step);
      dot.classList.toggle('is-done', idx + 1 < state.step);
    });

    prevBtn.hidden = state.step === 1;
    const lastStep = steps.length;
    const isFinal = state.step === lastStep;
    nextBtn.hidden = isFinal;
    submitBtn.hidden = !isFinal;
  };

  form.querySelectorAll('[data-name]').forEach((group) => {
    const name = group.getAttribute('data-name');
    group.querySelectorAll('.strategy-option').forEach((button) => {
      button.addEventListener('click', () => {
        const value = button.getAttribute('data-value') || '';
        form.elements[name].value = value;
        syncOptionState(name, value);

        if (name === 'persona_group') {
          updateSubtypes();
          applyPersonaDefaults();
        }
      });
    });
  });

  document.getElementById('persona_subtype')?.addEventListener('change', refreshSynthesis);
  form.querySelector('#custom_context')?.addEventListener('input', refreshSynthesis);

  prevBtn.addEventListener('click', () => {
    state.step = Math.max(1, state.step - 1);
    setError('');
    render();
  });

  nextBtn.addEventListener('click', () => {
    if (!validateStep(state.step)) return;
    state.step = Math.min(steps.length, state.step + 1);
    render();
  });

  quickBtn.addEventListener('click', () => {
    state.quickMode = !state.quickMode;
    quickModeField.value = state.quickMode ? '1' : '0';
    quickBtn.classList.toggle('is-active', state.quickMode);
    setWarning(state.quickMode ? 'Mode rapide activé : seules les étapes Objectif et Cible sont obligatoires.' : '');
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!validateStep(1) || !validateStep(2)) return;
    if (!state.quickMode && (!validateStep(3) || !validateStep(4))) return;

    setError('');
    setWarning(state.quickMode ? 'Mode rapide activé : l’IA complètera le cadrage avec des hypothèses.' : '');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Analyse en cours...';

    const payload = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: payload,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      const json = await response.json();
      if (!response.ok || !json.success) {
        throw new Error(json.error || 'Impossible de lancer l’analyse.');
      }

      const data = json.data || {};
      document.getElementById('awareness-badge').textContent = data.awareness_level || 'N/A';
      document.getElementById('summary').textContent = data.summary || '';
      document.getElementById('guided-summary').textContent = data.guided_summary || '';

      ['pain-points', 'desires', 'content-angles', 'recommended-hooks'].forEach((id) => {
        const key = id.replace(/-/g, '_');
        const list = document.getElementById(id);
        const items = data[key] || [];
        list.innerHTML = items.map((item) => `<li>${item}</li>`).join('');
      });

      resultCard.hidden = false;
      resultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

      if (json.meta?.warning) {
        setWarning(json.meta.warning);
      }
    } catch (error) {
      setError(error.message || 'Une erreur est survenue.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Lancer l’analyse IA';
    }
  });

  createStepper();
  refreshSynthesis();
  render();
})();
