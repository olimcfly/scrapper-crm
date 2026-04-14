<?php
$pagesData = is_array($pages ?? null) ? $pages : [];
$summary = is_array($foundationSummary ?? null) ? $foundationSummary : [];
$completionData = is_array($completion ?? null) ? $completion : ['percent' => 0];
?>
<div class="public-pages-page">
  <header class="card">
    <h1>Pages publiques</h1>
    <p class="subtitle">Transforme ta Fondation stratégique en pages partageables (offre, présentation, promesse).</p>
    <div class="row wrap">
      <a class="btn btn-primary" href="/pages-publiques?action=create&type=offer">Générer page offre</a>
      <a class="btn btn-secondary" href="/pages-publiques?action=create&type=presentation">Générer page présentation</a>
      <a class="btn btn-secondary" href="/pages-publiques?action=create&type=promise">Générer page promesse</a>
      <a class="btn btn-secondary" href="/fondation-strategique">Mettre à jour depuis Fondation</a>
    </div>
    <p class="muted">Complétion Fondation: <?= (int) ($completionData['percent'] ?? 0) ?>% · Offre: <?= htmlspecialchars((string) ($summary['offer_name'] ?? 'Non définie')) ?></p>
  </header>
  <?php if (!empty($successMessage)): ?><div class="global-state success"><span class="state-dot"></span><p><?= htmlspecialchars((string) $successMessage) ?></p></div><?php endif; ?>
  <?php if (!empty($warningMessage)): ?><div class="global-state warning"><span class="state-dot"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div><?php endif; ?>

  <section class="card">
    <div class="card-header"><h2>Liste des pages</h2></div>
    <?php if ($pagesData === []): ?>
      <p class="muted">Aucune page pour le moment.</p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Type</th><th>Titre</th><th>Statut</th><th>Slug</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($pagesData as $page): ?>
            <tr>
              <td><?= htmlspecialchars((string) ($page['type'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($page['title'] ?? '')) ?></td>
              <td><span class="badge <?= (($page['status'] ?? '') === 'published') ? 'badge-active' : 'badge-placeholder' ?>"><?= htmlspecialchars((string) ($page['status'] ?? 'draft')) ?></span></td>
              <td><code><?= htmlspecialchars((string) ($page['slug'] ?? '')) ?></code></td>
              <td class="row wrap">
                <a class="btn btn-secondary btn-compact" href="/pages-publiques/<?= (int) ($page['id'] ?? 0) ?>/edit">Modifier</a>
                <?php if (($page['status'] ?? '') === 'published'): ?>
                  <a class="btn btn-secondary btn-compact" target="_blank" href="/p/<?= htmlspecialchars((string) ($page['slug'] ?? '')) ?>">Aperçu</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
