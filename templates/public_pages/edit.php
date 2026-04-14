<?php $p = is_array($page ?? null) ? $page : []; ?>
<div class="public-page-edit">
  <header class="card"><h1>Modifier page publique</h1><p class="subtitle">Publie, dépublie, ajuste ton slug et le contenu.</p></header>
  <?php if (!empty($warningMessage)): ?><div class="global-state warning"><span class="state-dot"></span><p><?= htmlspecialchars((string) $warningMessage) ?></p></div><?php endif; ?>
  <form method="post" action="/pages-publiques/<?= (int) ($p['id'] ?? 0) ?>/edit" class="card stack-md">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
    <div class="settings-grid two-cols">
      <label class="form-field"><span>Type</span><select name="type"><option value="offer" <?= (($p['type'] ?? '') === 'offer') ? 'selected' : '' ?>>Offre</option><option value="presentation" <?= (($p['type'] ?? '') === 'presentation') ? 'selected' : '' ?>>Présentation</option><option value="promise" <?= (($p['type'] ?? '') === 'promise') ? 'selected' : '' ?>>Promesse</option></select></label>
      <label class="form-field"><span>Statut</span><select name="status"><option value="draft" <?= (($p['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Brouillon</option><option value="published" <?= (($p['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option></select></label>
      <label class="form-field"><span>Titre</span><input type="text" name="title" value="<?= htmlspecialchars((string) ($p['title'] ?? '')) ?>"></label>
      <label class="form-field"><span>Sous-titre</span><input type="text" name="subtitle" value="<?= htmlspecialchars((string) ($p['subtitle'] ?? '')) ?>"></label>
      <label class="form-field settings-grid-full"><span>Slug</span><input type="text" name="slug" value="<?= htmlspecialchars((string) ($p['slug'] ?? '')) ?>"></label>
      <label class="form-field settings-grid-full"><span>Contenu HTML</span><textarea name="body_html" rows="18"><?= htmlspecialchars((string) ($p['body_html'] ?? '')) ?></textarea></label>
    </div>
    <div class="row wrap">
      <button class="btn btn-primary" type="submit">Enregistrer</button>
      <a class="btn btn-secondary" href="/pages-publiques">Retour</a>
      <a class="btn btn-secondary" target="_blank" href="/p/<?= htmlspecialchars((string) ($p['slug'] ?? '')) ?>?print=1">Version imprimable</a>
    </div>
  </form>
</div>
