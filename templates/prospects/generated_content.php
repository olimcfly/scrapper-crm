<?php
$typeLabels = [
  'post' => 'Post réseau social',
  'email' => 'Email',
  'message_court' => 'Message court (DM/WhatsApp/SMS)',
];

$variants = is_array($payload['variants'] ?? null) ? $payload['variants'] : [];
?>

<div class="page">
  <div class="container">

    <!-- HEADER -->
    <div class="page-header">
      <h1>Génération de contenu</h1>
      <p class="subtitle">Créer des contenus optimisés à partir de l’analyse IA</p>
    </div>

    <!-- GLOBAL STATES -->
    <?php if (!empty($successMessage)): ?>
      <div class="global-state success">
        <span class="state-dot"></span>
        <p><?= htmlspecialchars((string) $successMessage) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($warningMessage)): ?>
      <div class="global-state warning">
        <span class="state-dot"></span>
        <p><?= htmlspecialchars((string) $warningMessage) ?></p>
      </div>
    <?php endif; ?>

    <!-- PROSPECT INFO -->
    <div class="card">
      <div class="card-header">
        <h3><?= htmlspecialchars((string) ($prospect['full_name'] ?? 'Prospect')) ?></h3>
      </div>

      <p class="muted">
        Niveau de conscience :
        <strong><?= htmlspecialchars((string) ($awarenessLevel ?? '')) ?></strong>
      </p>

      <div class="badge">
        Type recommandé : <?= htmlspecialchars((string) ($recommendedType ?? '')) ?>
      </div>
    </div>

    <!-- CHOIX TYPE -->
    <?php if (($type ?? '') === ''): ?>

      <div class="card">
        <div class="card-header">
          <h3>Choisir le type de contenu</h3>
        </div>

        <div class="stack">
          <?php foreach ($typeLabels as $typeKey => $label): ?>
            <a class="btn btn-primary"
               href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents?type=<?= urlencode($typeKey) ?>">
              <?= htmlspecialchars($label) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

    <!-- FORM GENERATION -->
    <?php elseif (($generated ?? null) === null): ?>

      <div class="card">
        <div class="card-header">
          <h3>Paramètres de génération</h3>
        </div>

        <form method="post"
              action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate"
              class="stack">

          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
          <input type="hidden" name="type" value="<?= htmlspecialchars((string) $type) ?>">

          <div class="form-group">
            <label>Niveau de conscience</label>
            <input class="input" name="awareness_level" value="<?= htmlspecialchars((string) ($awarenessLevel ?? '')) ?>">
          </div>

          <div class="form-group">
            <label>Pain points</label>
            <textarea class="input" name="pain_points"><?= htmlspecialchars((string) ($prospect['blocages'] ?? '')) ?></textarea>
          </div>

          <div class="form-group">
            <label>Désirs</label>
            <textarea class="input" name="desires"><?= htmlspecialchars((string) ($prospect['notes_summary'] ?? '')) ?></textarea>
          </div>

          <div class="form-group">
            <label>Angle</label>
            <input class="input" name="angle">
          </div>

          <div class="form-group">
            <label>Hook</label>
            <input class="input" name="hook">
          </div>

          <div class="form-group">
            <label>Canal</label>
            <input class="input" name="channel" value="interaction récente">
          </div>

          <button class="btn btn-primary" type="submit">
            Générer 3 variantes
          </button>

        </form>
      </div>

    <!-- RESULTATS -->
    <?php else: ?>

      <div class="card">
        <div class="card-header">
          <h3>Brouillons générés</h3>
        </div>

        <p class="muted">
          <?= count($variants) ?> variantes générées
        </p>

        <div class="stack">

          <?php foreach ($variants as $index => $variant): ?>
            <?php
              $composed = trim(
                (string) ($variant['title'] ?? '') . "\n" .
                (string) ($variant['subject'] ?? '') . "\n" .
                (string) ($variant['opening'] ?? '') . "\n\n" .
                (string) ($variant['body'] ?? '') . "\n\n" .
                (string) ($variant['closing'] ?? '') . "\n" .
                (string) ($variant['cta'] ?? '')
              );
            ?>

            <article class="card small">

              <div class="card-header">
                <h4><?= htmlspecialchars((string) ($variant['label'] ?? ('Variante ' . ($index + 1)))) ?></h4>
              </div>

              <pre id="variant-<?= (int) $index ?>">
<?= htmlspecialchars($composed) ?>
              </pre>

              <div class="row">
                <button class="btn btn-secondary" type="button" onclick="copyVariant(<?= (int) $index ?>)">Copier</button>
                <a class="btn btn-primary" href="/messages-ia" onclick="copyVariant(<?= (int) $index ?>)">Utiliser</a>
              </div>

            </article>

          <?php endforeach; ?>

        </div>

        <form method="post"
              action="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>/generated-contents/generate"
              class="mt-sm">

          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
          <input type="hidden" name="type" value="<?= htmlspecialchars((string) ($generated['type'] ?? $type)) ?>">

          <button class="btn btn-secondary" type="submit">
            Régénérer
          </button>
        </form>

      </div>

    <?php endif; ?>

    <!-- FOOT -->
    <div class="card">
      <a class="btn btn-secondary" href="/prospects/<?= (int) ($prospect['id'] ?? 0) ?>">
        Retour au prospect
      </a>
    </div>

  </div>
</div>

<script>
async function copyVariant(index) {
  const target = document.getElementById('variant-' + index);
  if (!target) return;
  try {
    await navigator.clipboard.writeText(target.innerText);
  } catch (e) {
    alert('Copie impossible');
  }
}
</script>