<?php
$catalog = is_array($strategyCatalog ?? null) ? $strategyCatalog : [];
$objectives = (array) ($catalog['objectives'] ?? []);
$personaGroups = (array) ($catalog['persona_groups'] ?? []);
$personaSubtypes = (array) ($catalog['persona_subtypes'] ?? []);
$offerTypes = (array) ($catalog['offer_types'] ?? []);
$maturityLevels = (array) ($catalog['maturity_levels'] ?? []);
$intentions = (array) ($catalog['intentions'] ?? []);
$defaultsByPersona = (array) ($catalog['defaults_by_persona'] ?? []);
?>

<div class="strategy-page">
  <section class="page-header strategy-page-header">
    <h1>Assistant stratégique guidé</h1>
    <p class="subtitle">Cadre ton analyse en 4 étapes pour obtenir une réponse IA plus utile, plus cohérente et plus actionnable.</p>
  </section>

  <div class="strategy-guided-layout">
    <section class="card strategy-guided-card">
      <div class="strategy-flow-head">
        <div>
          <p class="strategy-overline">Analyse prospect</p>
          <h2>Construis ton cadrage avant de lancer l’IA</h2>
          <p class="strategy-help">Avant de lancer l’analyse, vérifie si ce cadrage te semble juste.</p>
        </div>
        <button type="button" id="strategy-quick-mode" class="btn btn-secondary btn-compact">Mode rapide</button>
      </div>

      <div class="strategy-stepper" id="strategy-stepper" aria-label="Étapes d'analyse"></div>

      <form id="strategy-analysis-form" method="post" action="/strategie/analyse" class="stack-md" novalidate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
        <input type="hidden" name="quick_mode" id="quick_mode" value="0">

        <section class="strategy-step is-active" data-step="1">
          <header class="strategy-step-head">
            <h3>Que veux-tu obtenir avec cette analyse ?</h3>
            <p>Choisis l’objectif principal, même si tu en as plusieurs.</p>
          </header>
          <div class="strategy-option-grid" data-name="objective">
            <?php foreach ($objectives as $key => $item): ?>
              <button type="button" class="strategy-option" data-value="<?= htmlspecialchars((string) $key) ?>">
                <strong><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></strong>
                <span><?= htmlspecialchars((string) ($item['hint'] ?? '')) ?></span>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="objective" id="objective" value="">
        </section>

        <section class="strategy-step" data-step="2">
          <header class="strategy-step-head">
            <h3>Qui cherches-tu à comprendre ou approcher ?</h3>
            <p>Le type de cible aide l’IA à mieux comprendre le contexte métier.</p>
          </header>
          <div class="strategy-option-grid" data-name="persona_group">
            <?php foreach ($personaGroups as $key => $item): ?>
              <button type="button" class="strategy-option" data-value="<?= htmlspecialchars((string) $key) ?>">
                <strong><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></strong>
                <span><?= htmlspecialchars((string) ($item['hint'] ?? '')) ?></span>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="persona_group" id="persona_group" value="">

          <div class="strategy-subtype" id="persona-subtype-wrap" hidden>
            <label for="persona_subtype">Sous-catégorie</label>
            <select name="persona_subtype" id="persona_subtype" class="input">
              <option value="">Choisir</option>
            </select>
          </div>
        </section>

        <section class="strategy-step" data-step="3">
          <header class="strategy-step-head">
            <h3>Que proposes-tu à cette cible ?</h3>
            <p>Une offre claire améliore la précision des recommandations.</p>
          </header>
          <div class="strategy-option-grid" data-name="offer_type">
            <?php foreach ($offerTypes as $key => $label): ?>
              <button type="button" class="strategy-option" data-value="<?= htmlspecialchars((string) $key) ?>">
                <strong><?= htmlspecialchars((string) $label) ?></strong>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="offer_type" id="offer_type" value="">
        </section>

        <section class="strategy-step" data-step="4">
          <header class="strategy-step-head">
            <h3>À ton avis, où en est cette personne aujourd’hui ?</h3>
            <p>Le niveau de maturité permet d’éviter des messages mal calibrés.</p>
          </header>
          <div class="strategy-option-list" data-name="maturity_level">
            <?php foreach ($maturityLevels as $key => $label): ?>
              <button type="button" class="strategy-option strategy-option-row" data-value="<?= htmlspecialchars((string) $key) ?>">
                <strong><?= htmlspecialchars((string) $label) ?></strong>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="maturity_level" id="maturity_level" value="">

          <div class="strategy-intention-wrap">
            <label>Ton intention</label>
            <div class="strategy-option-list" data-name="contact_intention">
              <?php foreach ($intentions as $key => $label): ?>
                <button type="button" class="strategy-option strategy-option-row" data-value="<?= htmlspecialchars((string) $key) ?>">
                  <strong><?= htmlspecialchars((string) $label) ?></strong>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <input type="hidden" name="contact_intention" id="contact_intention" value="">

          <div class="form-field">
            <label for="custom_context">Contexte complémentaire (optionnel)</label>
            <textarea id="custom_context" name="custom_context" rows="4" class="input" placeholder="Ex : zone géographique, saisonnalité, contrainte budgétaire, nuance métier..."></textarea>
          </div>
        </section>

        <section class="strategy-step" data-step="5">
          <header class="strategy-step-head">
            <h3>Synthèse avant lancement</h3>
          </header>

          <div class="strategy-synthesis" id="strategy-synthesis"></div>

          <div class="strategy-recommendation" id="strategy-recommendation">
            <strong>Stratégie recommandée</strong>
            <p id="strategy-recommendation-text">Sélectionne au moins un objectif et une cible pour obtenir une recommandation dynamique.</p>
          </div>
        </section>

        <div class="strategy-flow-actions">
          <button type="button" id="strategy-prev" class="btn btn-secondary">Retour</button>
          <button type="button" id="strategy-next" class="btn btn-primary">Suivant</button>
          <button type="submit" id="strategy-submit" class="btn btn-primary" hidden>Lancer l’analyse IA</button>
        </div>

        <p id="analysis-error" class="strategy-feedback error" hidden></p>
        <p id="analysis-warning" class="strategy-feedback warning" hidden></p>
      </form>
    </section>

    <aside class="strategy-side-panel">
      <section class="card strategy-result-card" id="analysis-result" hidden>
        <div class="card-header">
          <h3>Résultat de l’analyse</h3>
          <span id="awareness-badge" class="badge"></span>
        </div>
        <p id="guided-summary" class="strategy-guided-summary"></p>
        <div class="strategy-result-grid">
          <article><h4>Résumé</h4><p id="summary"></p></article>
          <article><h4>Pain points</h4><ul id="pain-points"></ul></article>
          <article><h4>Désirs profonds</h4><ul id="desires"></ul></article>
          <article><h4>Angles de contenu</h4><ul id="content-angles"></ul></article>
          <article><h4>Hooks marketing</h4><ul id="recommended-hooks"></ul></article>
        </div>
      </section>


      <section class="card">
        <div class="card-header"><h3>Ponts utiles</h3></div>
        <div class="row wrap">
          <a class="btn btn-secondary btn-compact" href="/fondation-strategique">Compléter ma fondation</a>
          <a class="btn btn-secondary btn-compact" href="/ressources">Voir les ressources utiles</a>
          <a class="btn btn-secondary btn-compact" href="/formation">Suivre une mini formation</a>
          <a class="btn btn-primary btn-compact" href="/pages-publiques">Créer une page publique</a>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <h3>Historique des analyses</h3>
        </div>

        <?php if (($history ?? []) === []): ?>
          <div class="empty-state small">
            <p class="muted">Aucune analyse pour le moment. Lance ton premier cadrage guidé.</p>
          </div>
        <?php else: ?>
          <div class="strategy-history stack-sm">
            <?php foreach (($history ?? []) as $item): ?>
              <?php
                $objectiveKey = (string) ($item['objective'] ?? '');
                $objectiveLabel = (string) (($objectives[$objectiveKey]['label'] ?? '') ?: 'Analyse libre');
              ?>
              <article class="strategy-history-item">
                <p class="muted"><?= htmlspecialchars((string) ($item['created_at'] ?? '')) ?></p>
                <strong><?= htmlspecialchars($objectiveLabel) ?></strong>
                <p><?= htmlspecialchars((string) ($item['summary'] ?? '')) ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    </aside>
  </div>
</div>

<script>
window.STRATEGY_CATALOG = <?= json_encode([
    'objectives' => $objectives,
    'persona_groups' => $personaGroups,
    'persona_subtypes' => $personaSubtypes,
    'offer_types' => $offerTypes,
    'maturity_levels' => $maturityLevels,
    'intentions' => $intentions,
    'defaults_by_persona' => $defaultsByPersona,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="/assets/js/strategy-guided.js"></script>
