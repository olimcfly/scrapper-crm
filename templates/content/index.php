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

$ideas3R = [
    [
        'title' => 'Réalité: "Pourquoi vos prospects restent silencieux même après un bon premier contact"',
        'angle' => 'Partir de la situation terrain: messages envoyés, peu de réponses, pipeline qui ralentit.',
        'motivation' => 'Montrer que le problème n’est pas le prospect mais le cadrage du message.',
        'explication' => [
            'internes' => [
                'Le prospect craint de se tromper de priorité et repousse la décision.',
                'Il ne voit pas encore clairement le gain immédiat pour son activité.',
            ],
            'externes' => [
                'Il reçoit trop de sollicitations similaires chaque semaine.',
                'Son agenda opérationnel écrase son temps de réflexion.',
            ],
            'injustices' => [
                'Des offres moins pertinentes passent devant grâce à un meilleur storytelling.',
                'Le prospect paie le prix d’informations incomplètes ou biaisées.',
            ],
        ],
        'recette' => 'Segmenter le message en 3 blocs: contexte réel, micro-victoire, prochaine étape concrète.',
        'exercice' => 'Réécris ton dernier message en retirant tout jargon et ajoute une preuve terrain en 1 phrase.',
        'fab' => [
            'feature' => 'Analyse prospect connectée aux pain points et aux désirs réels.',
            'advantage' => 'Chaque proposition de contenu part d’un besoin concret plutôt que d’un template générique.',
            'benefit' => 'Le prospect se sent compris, répond plus vite et la relation avance vers un rendez-vous qualifié.',
        ],
    ],
    [
        'title' => 'Recherche de solution: "La structure simple qui transforme une idée floue en contenu qui convertit"',
        'angle' => 'Positionner la méthode M.E.R.E comme accélérateur de clarté éditoriale.',
        'motivation' => 'Donner une méthode immédiatement actionnable pour éviter la page blanche.',
        'explication' => [
            'internes' => [
                'Le prospect doute de sa capacité à publier régulièrement.',
                'Il confond encore valeur perçue et longueur du contenu.',
            ],
            'externes' => [
                'Les algorithmes favorisent les contenus structurés et précis.',
                'Les équipes commerciales demandent des supports réutilisables vite.',
            ],
            'injustices' => [
                'Les experts terrain restent invisibles face aux créateurs plus bruyants.',
                'Un bon service est souvent jugé sur une communication trop faible.',
            ],
        ],
        'recette' => 'Utilise M pour capter le besoin, E pour expliquer, R pour guider, E pour faire passer à l’action.',
        'exercice' => 'Prends une idée de post LinkedIn et transforme-la en 4 paragraphes M.E.R.E en 10 minutes.',
        'fab' => [
            'feature' => 'Génération de structures éditoriales prêtes à adapter par canal.',
            'advantage' => 'Tu gardes une cohérence de message entre post, email et vidéo sans repartir de zéro.',
            'benefit' => 'Ton équipe publie plus sereinement, gagne du temps et crée enfin une continuité dans le tunnel de conversion.',
        ],
    ],
    [
        'title' => 'Risque à éviter: "Créer du contenu sans angle prospect = produire beaucoup, convaincre peu"',
        'angle' => 'Alerter sur le coût caché du contenu non relié à la réalité commerciale.',
        'motivation' => 'Prévenir la dispersion et recentrer sur la stratégie prospect.',
        'explication' => [
            'internes' => [
                'Le prospect perçoit un message trop centré sur l’offre et décroche.',
                'Il manque un fil conducteur entre problème et solution proposée.',
            ],
            'externes' => [
                'Les décisions d’achat sont plus lentes et demandent plus de preuves.',
                'La concurrence multiplie les contenus mais peu sont réellement utiles.',
            ],
            'injustices' => [
                'Des acheteurs motivés passent à côté d’une vraie solution par manque de pédagogie.',
                'Les équipes commerciales compensent par plus de relances au lieu de meilleurs contenus.',
            ],
        ],
        'recette' => 'Avant publication: vérifier 1 problème réel, 1 bénéfice transformationnel, 1 CTA concret.',
        'exercice' => 'Audit de ton dernier contenu: coche ce qui parle du prospect et retire ce qui parle uniquement de toi.',
        'fab' => [
            'feature' => 'Historique des contenus avec recherche par analyse, type et date.',
            'advantage' => 'Tu identifies vite ce qui performe et tu dupliques les formats gagnants.',
            'benefit' => 'Au lieu de repartir de zéro, tu construis une machine de contenu cumulative orientée résultats commerciaux.',
        ],
    ],
];

$templateCards = [
    ['name' => '3R', 'definition' => 'Structure de titres orientée réalité, solution, risque.', 'purpose' => 'Créer des hooks alignés au vécu prospect.', 'when' => 'Avant de rédiger un post, email ou script.', 'example' => '"Réalité: vos leads répondent peu" → "Solution: 1 séquence simple" → "Risque: relancer sans angle."'],
    ['name' => 'MERE', 'definition' => 'Motivation, Explication, Recette, Exercice.', 'purpose' => 'Transformer une idée en contenu pédagogique et actionnable.', 'when' => 'Pour des contenus éducatifs à forte valeur.', 'example' => 'Motiver sur un blocage réel, expliquer, donner le plan, proposer un exercice rapide.'],
    ['name' => 'FAB', 'definition' => 'Feature, Advantage, Benefit (transformation).', 'purpose' => 'Passer des caractéristiques produit aux bénéfices vécus.', 'when' => 'Pages offres, emails commerciaux, scripts de vente.', 'example' => 'Feature: dashboard IA → Advantage: priorisation auto → Benefit: moins de relances inutiles, plus de rendez-vous qualifiés.'],
    ['name' => 'AIDA', 'definition' => 'Attention, Intérêt, Désir, Action.', 'purpose' => 'Structurer un message de conversion simple.', 'when' => 'Landing pages, séquences d’emails, annonces.', 'example' => 'Attention par une vérité terrain, désir via preuve, action avec CTA unique.'],
    ['name' => 'PAS', 'definition' => 'Problème, Agitation, Solution.', 'purpose' => 'Rendre un problème tangible puis proposer une issue.', 'when' => 'Posts courts et accroches vidéo.', 'example' => 'Problème: peu de réponses, agitation: pipeline qui stagne, solution: framework 3R + MERE.'],
];

$resourceCards = [
    ['title' => 'Comprendre la formule 3R', 'explanation' => 'Les 3R servent à créer des titres qui collent à la réalité commerciale du prospect.', 'example' => 'Exemple: "Réalité: vos relances restent sans réponse".', 'checklist' => ['Parle d’une situation vécue.', 'Promet une piste claire.', 'Montre un risque réel à éviter.']],
    ['title' => 'Comprendre la structure MERE', 'explanation' => 'MERE guide la pédagogie: motiver, expliquer, donner la recette puis faire pratiquer.', 'example' => 'Exemple: post LinkedIn en 4 paragraphes, chacun avec une fonction claire.', 'checklist' => ['1 idée par section.', 'Explication concrète.', 'Exercice réalisable en moins de 10 min.']],
    ['title' => 'Transformer une caractéristique en bénéfice avec FAB', 'explanation' => 'Le bénéfice doit raconter un changement vécu côté prospect.', 'example' => '"Automatisation des brouillons" devient "2h gagnées pour préparer les rendez-vous chauds".', 'checklist' => ['Feature factuelle.', 'Advantage mesurable.', 'Benefit exprimé en transformation.']],
    ['title' => 'Interne, externe, injustice: faire la différence', 'explanation' => 'Un message fort combine les freins psychologiques, contextuels et systémiques.', 'example' => 'Interne: peur de mal choisir / Externe: manque de temps / Injustice: meilleur acteur moins visible.', 'checklist' => ['2 problèmes internes.', '2 externes.', '2 injustices.']],
    ['title' => 'Adapter le message selon le canal', 'explanation' => 'Le fond reste identique, la forme change selon la plateforme.', 'example' => 'LinkedIn: preuve + perspective. Email: clarté + CTA unique. WhatsApp: court + concret.', 'checklist' => ['Facebook: ton conversationnel.', 'LinkedIn: posture experte.', 'Email: structure lisible.', 'WhatsApp: phrases courtes.', 'Vidéo: hook dans les 5 premières secondes.']],
    ['title' => 'Exemples avant / après', 'explanation' => 'Comparer deux versions accélère la montée en compétence.', 'example' => 'Avant: "Notre outil est innovant." Après: "Vous transformez vos notes prospects en messages prêts à envoyer."', 'checklist' => ['Version avant trop vague.', 'Version après orientée résultat.', 'CTA clair en fin de message.']],
    ['title' => 'Mini exercices pratiques', 'explanation' => 'Pratiquer régulièrement crée des automatismes éditoriaux solides.', 'example' => 'Exercice: reformuler un argument produit en bénéfice transformationnel.', 'checklist' => ['Timer 7 minutes.', '1 canal cible.', '1 CTA final.']],
];
?>

<div class="studio-page">
  <header class="studio-header card">
    <h1>Studio de Contenu IA</h1>
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
    <button type="button" class="studio-tab" data-studio-tab="ressources">Ressources</button>
    <button type="button" class="studio-tab" data-studio-tab="historique">Historique</button>
  </nav>

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
            <p class="muted">Analyse connectée: contenu orienté réalité terrain.</p>
          </div>
          <span class="badge"><?= safe_string($analysisData['awareness_level'] ?? 'Niveau non défini') ?></span>
        </div>

        <div class="studio-brief-grid">
          <div class="studio-brief-item">
            <strong>Résumé prospect</strong>
            <p><?= nl2br(safe_string($analysisData['summary'] ?? '')) ?></p>
          </div>
          <div class="studio-brief-item">
            <strong>Douleurs prioritaires</strong>
            <p><?= safe_string($primaryPain) ?></p>
            <p><?= safe_string($secondaryPain) ?></p>
          </div>
          <div class="studio-brief-item">
            <strong>Résultats recherchés</strong>
            <p><?= safe_string($primaryDesire) ?></p>
            <p><?= safe_string($secondaryDesire) ?></p>
          </div>
          <div class="studio-brief-item">
            <strong>Angle suggéré</strong>
            <p><?= safe_string($primaryAngle) ?></p>
          </div>
        </div>
      </article>

      <article class="card">
        <div class="card-header">
          <div>
            <h2>Idées de titres 3R</h2>
            <p class="muted">Chaque idée inclut un angle clair, puis un développement M.E.R.E + FAB.</p>
          </div>
        </div>

        <div class="studio-idea-list">
          <?php foreach ($ideas3R as $index => $idea): ?>
            <article class="studio-idea-card">
              <h3><?= safe_string($idea['title']) ?></h3>
              <p class="muted"><?= safe_string($idea['angle']) ?></p>

              <div class="studio-actions">
                <button class="btn btn-secondary btn-compact" type="button" data-copy-text="<?= safe_string($idea['title'] . ' — ' . $idea['angle']) ?>">Copier</button>
                <button class="btn btn-primary btn-compact" type="button" data-expand-id="mere-<?= $index ?>">Développer</button>
              </div>

              <section class="studio-expand" id="mere-<?= $index ?>" hidden>
                <div class="studio-framework-block">
                  <h4>M — Motivation</h4>
                  <p><?= safe_string($idea['motivation']) ?></p>
                </div>
                <div class="studio-framework-block">
                  <h4>E — Explication</h4>
                  <ul>
                    <?php foreach (($idea['explication']['internes'] ?? []) as $item): ?><li><strong>Interne:</strong> <?= safe_string($item) ?></li><?php endforeach; ?>
                    <?php foreach (($idea['explication']['externes'] ?? []) as $item): ?><li><strong>Externe:</strong> <?= safe_string($item) ?></li><?php endforeach; ?>
                    <?php foreach (($idea['explication']['injustices'] ?? []) as $item): ?><li><strong>Injustice:</strong> <?= safe_string($item) ?></li><?php endforeach; ?>
                  </ul>
                </div>
                <div class="studio-framework-block">
                  <h4>R — Recette</h4>
                  <p><?= safe_string($idea['recette']) ?></p>
                </div>
                <div class="studio-framework-block">
                  <h4>E — Exercice</h4>
                  <p><?= safe_string($idea['exercice']) ?></p>
                </div>

                <div class="studio-framework-block studio-fab">
                  <h4>FAB orienté transformation</h4>
                  <p><strong>Feature:</strong> <?= safe_string($idea['fab']['feature'] ?? '') ?></p>
                  <p><strong>Advantage:</strong> <?= safe_string($idea['fab']['advantage'] ?? '') ?></p>
                  <p><strong>Benefit (transformation):</strong> <?= safe_string($idea['fab']['benefit'] ?? '') ?></p>
                </div>

                <div class="studio-actions wrap">
                  <button class="btn btn-secondary btn-compact" type="button" data-copy-section="mere-<?= $index ?>">Copier</button>
                  <button class="btn btn-secondary btn-compact" type="button">Transformer en post</button>
                  <button class="btn btn-secondary btn-compact" type="button">Transformer en email</button>
                  <button class="btn btn-secondary btn-compact" type="button">Transformer en script vidéo</button>
                  <button class="btn btn-primary btn-compact" type="button">Générer message</button>
                </div>
              </section>
            </article>
          <?php endforeach; ?>
        </div>
      </article>

      <article class="card">
        <div class="card-header">
          <div>
            <h2>Génération rapide connectée</h2>
            <p class="muted">Transforme l’analyse en brouillon exploitable immédiatement.</p>
          </div>
        </div>

        <form method="post" action="/contenu/generer" class="studio-generator-form">
          <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">
          <div class="studio-form-grid">
            <label class="form-field"><span>Type de contenu</span>
              <select name="content_type" class="input select">
                <option value="post" <?= ($optionsData['content_type'] ?? 'post') === 'post' ? 'selected' : '' ?>>Post</option>
                <option value="email" <?= ($optionsData['content_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                <option value="message_court" <?= ($optionsData['content_type'] ?? '') === 'message_court' ? 'selected' : '' ?>>Message court</option>
              </select>
            </label>

            <label class="form-field"><span>Canal</span>
              <select name="channel" class="input select">
                <option value="linkedin" <?= ($optionsData['channel'] ?? 'linkedin') === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                <option value="facebook" <?= ($optionsData['channel'] ?? '') === 'facebook' ? 'selected' : '' ?>>Facebook</option>
                <option value="instagram" <?= ($optionsData['channel'] ?? '') === 'instagram' ? 'selected' : '' ?>>Instagram</option>
                <option value="email" <?= ($optionsData['channel'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                <option value="whatsapp" <?= ($optionsData['channel'] ?? '') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
              </select>
            </label>

            <label class="form-field"><span>Objectif</span>
              <select name="objective" class="input select">
                <option value="attirer" <?= ($optionsData['objective'] ?? 'attirer') === 'attirer' ? 'selected' : '' ?>>Attirer</option>
                <option value="faire_reagir" <?= ($optionsData['objective'] ?? '') === 'faire_reagir' ? 'selected' : '' ?>>Faire réagir</option>
                <option value="convertir" <?= ($optionsData['objective'] ?? '') === 'convertir' ? 'selected' : '' ?>>Convertir</option>
              </select>
            </label>

            <label class="form-field"><span>Ton</span>
              <select name="tone" class="input select">
                <option value="simple" <?= ($optionsData['tone'] ?? 'simple') === 'simple' ? 'selected' : '' ?>>Simple</option>
                <option value="directe" <?= ($optionsData['tone'] ?? '') === 'directe' ? 'selected' : '' ?>>Direct</option>
                <option value="experte" <?= ($optionsData['tone'] ?? '') === 'experte' ? 'selected' : '' ?>>Expert</option>
                <option value="chaleureuse" <?= ($optionsData['tone'] ?? '') === 'chaleureuse' ? 'selected' : '' ?>>Chaleureux</option>
              </select>
            </label>
          </div>

          <div class="studio-actions">
            <button class="btn btn-primary" type="submit">Générer un brouillon</button>
          </div>
        </form>

        <?php if ($generatedData !== null): ?>
          <div class="studio-output">
            <h3>Résultat généré</h3>
            <pre><?= safe_string((string) ($generatedData['content'] ?? '')) ?></pre>
          </div>
        <?php endif; ?>
      </article>
    <?php endif; ?>
  </section>

  <section class="studio-panel" data-studio-panel="modeles" hidden>
    <article class="card">
      <div class="card-header">
        <div>
          <h2>Bibliothèque de modèles</h2>
          <p class="muted">Des structures prêtes à l’emploi pour accélérer création et conversion.</p>
        </div>
      </div>

      <div class="studio-model-grid">
        <?php foreach ($templateCards as $template): ?>
          <article class="studio-model-card">
            <h3><?= safe_string($template['name']) ?></h3>
            <p><strong>Définition:</strong> <?= safe_string($template['definition']) ?></p>
            <p><strong>Utilité:</strong> <?= safe_string($template['purpose']) ?></p>
            <p><strong>Quand l’utiliser:</strong> <?= safe_string($template['when']) ?></p>
            <p><strong>Mini exemple:</strong> <?= safe_string($template['example']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </article>
  </section>

  <section class="studio-panel" data-studio-panel="ressources" hidden>
    <article class="card">
      <div class="card-header">
        <div>
          <h2>Ressources & Méthodes de Contenu</h2>
          <p class="muted">Apprendre à mieux écrire, mieux structurer et mieux convertir sans jargon inutile.</p>
        </div>
      </div>

      <div class="studio-resource-grid">
        <?php foreach ($resourceCards as $resource): ?>
          <article class="studio-resource-card">
            <h3><?= safe_string($resource['title']) ?></h3>
            <p><?= safe_string($resource['explanation']) ?></p>
            <p><strong>Exemple concret:</strong> <?= safe_string($resource['example']) ?></p>
            <div>
              <strong>Checklist / mini exercice</strong>
              <ul>
                <?php foreach (($resource['checklist'] ?? []) as $line): ?>
                  <li><?= safe_string($line) ?></li>
                <?php endforeach; ?>
              </ul>
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
          <p class="muted">Retrouve, filtre et réutilise tes contenus par analyse, type et date.</p>
        </div>
      </div>

      <?php if ($historyData === []): ?>
        <div class="empty-state">
          <p>Aucun contenu généré pour le moment.</p>
        </div>
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
            <article
              class="studio-history-item"
              data-analysis="<?= safe_string(mb_strtolower($analysisSummary)) ?>"
              data-type="<?= safe_string($contentType) ?>"
              data-date="<?= safe_string($isoDate) ?>"
            >
              <p class="muted"><?= safe_string($createdAt) ?> · <?= safe_string($contentType) ?></p>
              <h3><?= safe_string($analysisSummary) ?></h3>
              <p><?= nl2br(safe_string($item['generated_content'] ?? '')) ?></p>
              <div class="studio-actions wrap">
                <a href="/contenu?draft_id=<?= (int) ($item['id'] ?? 0) ?>" class="btn btn-secondary btn-compact">Réouvrir</a>
                <form method="post" action="/contenu/dupliquer">
                  <input type="hidden" name="_csrf" value="<?= safe_string($csrfToken ?? '') ?>">
                  <input type="hidden" name="draft_id" value="<?= (int) ($item['id'] ?? 0) ?>">
                  <button class="btn btn-secondary btn-compact" type="submit">Dupliquer</button>
                </form>
                <button class="btn btn-secondary btn-compact" type="button" data-local-delete="1">Supprimer</button>
                <button class="btn btn-primary btn-compact" type="button" data-export-item="1">Exporter</button>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
  </section>
</div>

<script src="/assets/js/content-studio.js"></script>
