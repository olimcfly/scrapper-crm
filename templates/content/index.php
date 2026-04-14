<?php

declare(strict_types=1);

/**
 * @param mixed $value
 * @return array<int|string, mixed>
 */
function safe_array(mixed $value): array
{
    return is_array($value) ? $value : [];
}

function safe_string(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$analysisData = safe_array($analysis ?? null);
$optionsData = safe_array($options ?? null);
$generatedData = is_array($generated ?? null) ? $generated : null;
$historyData = safe_array($history ?? null);

$painPoints = array_values(array_filter(array_map(static fn (mixed $item): string => trim((string) $item), safe_array($analysisData['pain_points'] ?? null))));
$desires = array_values(array_filter(array_map(static fn (mixed $item): string => trim((string) $item), safe_array($analysisData['desires'] ?? null))));
$contentAngles = array_values(array_filter(array_map(static fn (mixed $item): string => trim((string) $item), safe_array($analysisData['content_angles'] ?? null))));

$primaryPain = $painPoints[0] ?? 'manque de méthode claire pour transformer ses échanges en opportunités';
$secondaryPain = $painPoints[1] ?? 'difficulté à garder une cadence de contenu utile';
$primaryDesire = $desires[0] ?? 'générer des conversations qualifiées sans y passer ses soirées';
$secondaryDesire = $desires[1] ?? 'avoir des messages plus clairs et crédibles';
$primaryAngle = $contentAngles[0] ?? 'clarifier la promesse et le passage à l’action';

$methodCards = [
    [
        'key' => '3R',
        'icon' => '🎯',
        'title' => '3R: capter l’attention en 3 angles qui convertissent',
        'subtitle' => 'Trouve le bon titre à partir de la Réalité, de la Réponse et du Risque.',
        'method' => [
            'Réalité: parle d’une situation vécue par le prospect.',
            'Réponse: propose une solution simple et crédible.',
            'Risque: montre ce qui se passe s’il ne change rien.',
        ],
        'example' => '"Réalité: vos leads répondent peu" → "Réponse: une séquence de 3 messages" → "Risque: pipeline à l’arrêt".',
        'exercise' => 'Écris 3 titres pour un même sujet en utilisant les 3R en moins de 5 minutes.',
        'focus' => 'Créer un hook 3R pour attirer plus de réponses qualifiées.',
        'content_type' => 'post',
        'channel' => 'linkedin',
        'objective' => 'attirer',
        'tone' => 'directe',
    ],
    [
        'key' => 'MERE',
        'icon' => '🧩',
        'title' => 'M.E.R.E: structurer un contenu pédagogique prêt à publier',
        'subtitle' => 'Passe d’une idée floue à un contenu guidé et actionnable.',
        'method' => [
            'Motivation: relie le sujet à une frustration réelle.',
            'Explication: clarifie le pourquoi en langage simple.',
            'Recette: donne une trame en étapes concrètes.',
            'Exercice: fais passer immédiatement à l’action.',
        ],
        'example' => 'Post LinkedIn: 1 paragraphe par lettre M.E.R.E avec un CTA final concret.',
        'exercise' => 'Prends ton dernier contenu et réécris-le en 4 blocs M.E.R.E.',
        'focus' => 'Transformer une idée brute en contenu M.E.R.E clair et actionnable.',
        'content_type' => 'post',
        'channel' => 'linkedin',
        'objective' => 'faire_reagir',
        'tone' => 'simple',
    ],
    [
        'key' => 'FAB',
        'icon' => '⚙️',
        'title' => 'FAB: vendre la transformation, pas les fonctionnalités',
        'subtitle' => 'Traduis ton offre en bénéfice concret pour ton prospect.',
        'method' => [
            'Feature: ce que ton offre fait concrètement.',
            'Advantage: pourquoi c’est mieux que l’approche actuelle.',
            'Benefit: quel résultat tangible le prospect obtient.',
        ],
        'example' => 'Feature: génération IA → Advantage: gain de temps → Benefit: plus de RDV qualifiés.',
        'exercise' => 'Liste 3 features de ton offre puis convertis-les en bénéfices clients.',
        'focus' => 'Reformuler mon offre avec FAB pour augmenter la conversion.',
        'content_type' => 'email',
        'channel' => 'email',
        'objective' => 'convertir',
        'tone' => 'experte',
    ],
    [
        'key' => 'Psychologie',
        'icon' => '🧠',
        'title' => 'Psychologie: lever les blocages internes, externes et systémiques',
        'subtitle' => 'Rends ton message plus humain, plus crédible, plus convaincant.',
        'method' => [
            'Interne: identifie la peur ou le doute principal.',
            'Externe: contextualise les contraintes terrain.',
            'Injustice: nomme ce qui freine injustement la décision.',
        ],
        'example' => 'Interne: peur de se tromper · Externe: surcharge · Injustice: bruit concurrentiel.',
        'exercise' => 'Écris 1 phrase par type de blocage pour ton prospect principal.',
        'focus' => 'Créer un message qui répond aux freins psychologiques de mon prospect.',
        'content_type' => 'message_court',
        'channel' => 'whatsapp',
        'objective' => 'rassurer',
        'tone' => 'chaleureuse',
    ],
];

$templateCards = [
    ['name' => '3R', 'definition' => 'Réalité, Réponse, Risque', 'purpose' => 'Hooks orientés terrain', 'when' => 'Accroches post, email, vidéo', 'example' => 'Réalité actuelle + piste claire + coût de l’inaction'],
    ['name' => 'MERE', 'definition' => 'Motivation, Explication, Recette, Exercice', 'purpose' => 'Contenu éducatif prêt à agir', 'when' => 'Posts pédagogiques, carrousels, newsletter', 'example' => '4 blocs courts qui guident le lecteur'],
    ['name' => 'FAB', 'definition' => 'Feature, Advantage, Benefit', 'purpose' => 'Transformer l’offre en impact client', 'when' => 'Pages offres, séquences de vente', 'example' => 'Caractéristique → avantage → bénéfice business'],
    ['name' => 'Psychologie', 'definition' => 'Interne, Externe, Injustice', 'purpose' => 'Messages plus persuasifs', 'when' => 'Objections, relances, scripts', 'example' => 'Répondre à la peur, au contexte et à la perception'],
];

$progressRatio = $generatedData !== null ? 100 : ($analysisData === [] ? 15 : 55);
?>

<div class="studio-page">
  <header class="studio-header card">
    <p class="studio-kicker">Atelier IA</p>
    <h1>Studio de Contenu IA</h1>
    <p class="studio-subtitle">Crée du contenu qui attire, engage et convertit avec des méthodes simples et guidées</p>

    <div class="studio-progress" aria-label="Progression atelier">
      <div class="studio-progress-meta">
        <strong>Progression atelier</strong>
        <span><?= (int) $progressRatio ?>%</span>
      </div>
      <div class="studio-progress-track"><span style="width: <?= (int) $progressRatio ?>%"></span></div>
    </div>
  </header>

  <?php if (!empty($successMessage)): ?>
    <div class="global-state success">
      <span class="state-dot"></span>
      <p><?= safe_string($successMessage) ?></p>
    </div>
  <?php endif; ?>

  <?php if (!empty($warningMessage)): ?>
    <div class="global-state warning">
      <span class="state-dot"></span>
      <p><?= safe_string($warningMessage) ?></p>
    </div>
  <?php endif; ?>

  <nav class="studio-tabs" aria-label="Sections du studio">
    <button type="button" class="studio-tab is-active" data-studio-tab="creer">Créer</button>
    <button type="button" class="studio-tab" data-studio-tab="modeles">Modèles</button>
    <button type="button" class="studio-tab" data-studio-tab="methodes">Méthodes</button>
    <button type="button" class="studio-tab" data-studio-tab="historique">Historique</button>
  </nav>

  <div class="studio-layout">
    <div class="studio-main">
      <section class="studio-panel is-active" data-studio-panel="creer">
        <?php if ($analysisData === []): ?>
          <div class="card empty-state-guided">
            <div class="empty-icon">🎯</div>
            <h2 class="empty-title">Aucune analyse prospect connectée</h2>
            <p class="empty-message">Le Studio s’appuie sur l’analyse stratégique pour générer des contenus plus pertinents et orientés conversion.</p>
            <a class="btn btn-primary empty-cta" href="/strategie">Créer une analyse prospect</a>
          </div>
        <?php else: ?>
          <article class="card studio-brief-card">
            <div class="card-header">
              <div>
                <h2>Point de départ stratégique</h2>
                <p class="muted">Atelier guidé par ton analyse prospect.</p>
              </div>
              <span class="badge"><?= safe_string($analysisData['awareness_level'] ?? 'Niveau non défini') ?></span>
            </div>

            <div class="studio-brief-grid">
              <div class="studio-brief-item"><strong>Douleur #1</strong><p><?= safe_string($primaryPain) ?></p></div>
              <div class="studio-brief-item"><strong>Douleur #2</strong><p><?= safe_string($secondaryPain) ?></p></div>
              <div class="studio-brief-item"><strong>Désir #1</strong><p><?= safe_string($primaryDesire) ?></p></div>
              <div class="studio-brief-item"><strong>Désir #2</strong><p><?= safe_string($secondaryDesire) ?></p></div>
              <div class="studio-brief-item full"><strong>Angle recommandé</strong><p><?= safe_string($primaryAngle) ?></p></div>
            </div>
          </article>

          <article class="card studio-create-card" id="atelier-create-form">
            <div class="card-header">
              <div>
                <h2>Créer mon contenu (mode atelier)</h2>
                <p class="muted">Sélectionne ton objectif, ajoute ton idée, puis génère immédiatement un brouillon sauvegardé.</p>
              </div>
            </div>

            <form method="post" action="/contenu/generer" class="studio-generator-form" id="studio-generate-form">
              <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">
              <input type="hidden" name="framework" id="studio-framework" value="<?= safe_string($optionsData['framework'] ?? '') ?>">

              <div class="studio-form-grid">
                <label class="form-field"><span>Type de contenu</span>
                  <select name="content_type" id="studio-content-type" class="input select">
                    <option value="post" <?= ($optionsData['content_type'] ?? 'post') === 'post' ? 'selected' : '' ?>>Post</option>
                    <option value="email" <?= ($optionsData['content_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                    <option value="message_court" <?= ($optionsData['content_type'] ?? '') === 'message_court' ? 'selected' : '' ?>>Message court</option>
                  </select>
                </label>

                <label class="form-field"><span>Canal</span>
                  <select name="channel" id="studio-channel" class="input select">
                    <option value="linkedin" <?= ($optionsData['channel'] ?? 'linkedin') === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                    <option value="facebook" <?= ($optionsData['channel'] ?? '') === 'facebook' ? 'selected' : '' ?>>Facebook</option>
                    <option value="instagram" <?= ($optionsData['channel'] ?? '') === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                    <option value="email" <?= ($optionsData['channel'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                    <option value="whatsapp" <?= ($optionsData['channel'] ?? '') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                  </select>
                </label>

                <label class="form-field"><span>Objectif</span>
                  <select name="objective" id="studio-objective" class="input select">
                    <option value="attirer" <?= ($optionsData['objective'] ?? 'attirer') === 'attirer' ? 'selected' : '' ?>>Attirer</option>
                    <option value="faire_reagir" <?= ($optionsData['objective'] ?? '') === 'faire_reagir' ? 'selected' : '' ?>>Faire réagir</option>
                    <option value="rassurer" <?= ($optionsData['objective'] ?? '') === 'rassurer' ? 'selected' : '' ?>>Rassurer</option>
                    <option value="convertir" <?= ($optionsData['objective'] ?? '') === 'convertir' ? 'selected' : '' ?>>Convertir</option>
                  </select>
                </label>

                <label class="form-field"><span>Ton</span>
                  <select name="tone" id="studio-tone" class="input select">
                    <option value="simple" <?= ($optionsData['tone'] ?? 'simple') === 'simple' ? 'selected' : '' ?>>Simple</option>
                    <option value="directe" <?= ($optionsData['tone'] ?? '') === 'directe' ? 'selected' : '' ?>>Direct</option>
                    <option value="experte" <?= ($optionsData['tone'] ?? '') === 'experte' ? 'selected' : '' ?>>Expert</option>
                    <option value="chaleureuse" <?= ($optionsData['tone'] ?? '') === 'chaleureuse' ? 'selected' : '' ?>>Chaleureux</option>
                  </select>
                </label>

                <label class="form-field full"><span>Idée / offre / message à travailler</span>
                  <textarea name="focus_input" id="studio-focus-input" class="input" rows="3" placeholder="Ex: Je veux transformer mes appels découverte en messages LinkedIn qui prennent des RDV."><?= safe_string($optionsData['focus_input'] ?? '') ?></textarea>
                </label>

                <label class="form-field full studio-guided-switch">
                  <input type="checkbox" name="guided_mode" value="1" <?= (($optionsData['guided_mode'] ?? '0') === '1') ? 'checked' : '' ?>>
                  <span>Mode apprentissage guidé (ajoute des étapes pédagogiques dans la sortie)</span>
                </label>
              </div>

              <div class="studio-actions">
                <button class="btn btn-primary" type="submit">Générer avec IA</button>
              </div>
            </form>

            <?php if ($generatedData !== null): ?>
              <div class="studio-output" id="studio-generated-output">
                <div class="card-header">
                  <div>
                    <h3>Résultat généré</h3>
                    <p class="muted">Brouillon sauvegardé automatiquement dans l’historique.</p>
                  </div>
                </div>
                <pre><?= safe_string((string) ($generatedData['content'] ?? '')) ?></pre>
                <div class="studio-actions wrap">
                  <button class="btn btn-secondary btn-compact" type="button" data-copy-target="studio-generated-output">Copier</button>
                  <a href="/messages-ia" class="btn btn-secondary btn-compact">Réutiliser dans Messages IA</a>
                  <a href="/contenu" class="btn btn-primary btn-compact">Réutiliser dans Contenu</a>
                </div>
              </div>
            <?php endif; ?>
          </article>
        <?php endif; ?>
      </section>

      <section class="studio-panel" data-studio-panel="modeles" hidden>
        <article class="card">
          <div class="card-header">
            <div>
              <h2>Modèles prêts à actionner</h2>
              <p class="muted">Sélectionne une structure, puis envoie-la directement dans l’atelier.</p>
            </div>
          </div>

          <div class="studio-model-grid">
            <?php foreach ($templateCards as $template): ?>
              <article class="studio-model-card">
                <h3><?= safe_string($template['name']) ?></h3>
                <p><strong>Méthode:</strong> <?= safe_string($template['definition']) ?></p>
                <p><strong>But:</strong> <?= safe_string($template['purpose']) ?></p>
                <p><strong>Quand:</strong> <?= safe_string($template['when']) ?></p>
                <p><strong>Exemple:</strong> <?= safe_string($template['example']) ?></p>
                <button
                  type="button"
                  class="btn btn-secondary btn-compact"
                  data-use-method
                  data-framework="<?= safe_string($template['name']) ?>"
                  data-focus="<?= safe_string('Appliquer le modèle ' . $template['name'] . ' à mon offre actuelle.') ?>"
                >Créer mon contenu</button>
              </article>
            <?php endforeach; ?>
          </div>
        </article>
      </section>

      <section class="studio-panel" data-studio-panel="methodes" hidden>
        <article class="card">
          <div class="card-header">
            <div>
              <h2>Méthodes actionnables</h2>
              <p class="muted">Chaque carte est un mini atelier: apprendre, pratiquer, générer.</p>
            </div>
          </div>

          <div class="studio-method-grid">
            <?php foreach ($methodCards as $method): ?>
              <article class="studio-method-card">
                <p class="studio-method-icon"><?= safe_string($method['icon']) ?> <?= safe_string($method['key']) ?></p>
                <h3><?= safe_string($method['title']) ?></h3>
                <p class="muted"><?= safe_string($method['subtitle']) ?></p>

                <div class="studio-method-block">
                  <h4>Méthode</h4>
                  <ul>
                    <?php foreach (($method['method'] ?? []) as $line): ?>
                      <li><?= safe_string($line) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <div class="studio-method-block studio-highlight">
                  <h4>Exemple concret</h4>
                  <p><?= safe_string($method['example']) ?></p>
                </div>

                <div class="studio-method-block">
                  <h4>Exercice rapide</h4>
                  <p><?= safe_string($method['exercise']) ?></p>
                </div>

                <div class="studio-actions wrap">
                  <button
                    type="button"
                    class="btn btn-primary btn-compact"
                    data-generate-method
                    data-framework="<?= safe_string($method['key']) ?>"
                    data-focus="<?= safe_string($method['focus']) ?>"
                    data-content-type="<?= safe_string($method['content_type']) ?>"
                    data-channel="<?= safe_string($method['channel']) ?>"
                    data-objective="<?= safe_string($method['objective']) ?>"
                    data-tone="<?= safe_string($method['tone']) ?>"
                  >Générer avec IA</button>
                  <button
                    type="button"
                    class="btn btn-secondary btn-compact"
                    data-use-method
                    data-framework="<?= safe_string($method['key']) ?>"
                    data-focus="<?= safe_string($method['focus']) ?>"
                    data-content-type="<?= safe_string($method['content_type']) ?>"
                    data-channel="<?= safe_string($method['channel']) ?>"
                    data-objective="<?= safe_string($method['objective']) ?>"
                    data-tone="<?= safe_string($method['tone']) ?>"
                  >Créer mon contenu</button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </article>
      </section>

      <section class="studio-panel" data-studio-panel="historique" hidden>
        <article class="card">
          <div class="card-header">
            <div>
              <h2>Historique des contenus générés</h2>
              <p class="muted">Retrouve, filtre et réutilise tes contenus dans tes autres modules.</p>
            </div>
          </div>

          <?php if ($historyData === []): ?>
            <div class="empty-state"><p>Aucun contenu généré pour le moment.</p></div>
          <?php else: ?>
            <div class="studio-history-filters">
              <input type="search" class="input" id="history-search-analysis" placeholder="Rechercher par analyse">
              <select class="input select" id="history-filter-type">
                <option value="">Type de contenu</option>
                <option value="post">Post</option>
                <option value="email">Email</option>
                <option value="message_court">Message court</option>
              </select>
              <input type="date" class="input" id="history-filter-date">
            </div>

            <div class="studio-history-list" id="studio-history-list">
              <?php foreach ($historyData as $item): ?>
                <?php
                  $analysisSummary = (string) ($item['analysis_summary'] ?? 'Analyse non précisée');
                  $contentType = (string) ($item['content_type'] ?? 'post');
                  $createdAt = (string) ($item['created_at'] ?? '');
                  $isoDate = $createdAt !== '' ? substr($createdAt, 0, 10) : '';
                ?>
                <article class="studio-history-item" data-analysis="<?= safe_string(mb_strtolower($analysisSummary)) ?>" data-type="<?= safe_string($contentType) ?>" data-date="<?= safe_string($isoDate) ?>">
                  <p class="muted"><?= safe_string($createdAt) ?> · <?= safe_string($contentType) ?></p>
                  <h3><?= safe_string($analysisSummary) ?></h3>
                  <p><?= nl2br(safe_string($item['generated_content'] ?? '')) ?></p>
                  <div class="studio-actions wrap">
                    <a href="/contenu?draft_id=<?= (int) ($item['id'] ?? 0) ?>" class="btn btn-secondary btn-compact">Réouvrir</a>
                    <a href="/messages-ia" class="btn btn-secondary btn-compact">Vers Messages IA</a>
                    <form method="post" action="/contenu/dupliquer">
                      <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">
                      <input type="hidden" name="draft_id" value="<?= (int) ($item['id'] ?? 0) ?>">
                      <button class="btn btn-secondary btn-compact" type="submit">Dupliquer</button>
                    </form>
                    <button class="btn btn-primary btn-compact" type="button" data-export-item="1">Exporter</button>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>
      </section>
    </div>

    <aside class="studio-assistant card" id="studio-assistant-panel">
      <div class="card-header">
        <div>
          <h2>Assistant IA</h2>
          <p class="muted">Teste une idée et génère un brouillon immédiatement.</p>
        </div>
      </div>

      <div class="studio-assistant-form">
        <label class="form-field"><span>Offre / message / idée</span>
          <textarea class="input" id="assistant-input" rows="4" placeholder="Ex: Je veux un email de relance qui rassure un prospect hésitant."></textarea>
        </label>
        <div class="studio-actions wrap">
          <button type="button" class="btn btn-primary btn-compact" id="assistant-generate">Générer</button>
          <button type="button" class="btn btn-secondary btn-compact" id="assistant-save">Sauvegarder</button>
        </div>
      </div>

      <div class="studio-assistant-result" id="assistant-result" hidden>
        <h3>Résultat Assistant</h3>
        <p id="assistant-result-text"></p>
      </div>
    </aside>
  </div>
</div>

<script src="/assets/js/content-studio.js"></script>
