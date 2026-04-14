<?php $p = is_array($page ?? null) ? $page : []; ?>
<div class="public-page-render <?= (isset($_GET['print']) && $_GET['print'] === '1') ? 'is-print' : '' ?>">
  <article class="public-doc card">
    <h1><?= htmlspecialchars((string) ($p['title'] ?? '')) ?></h1>
    <?php if (!empty($p['subtitle'])): ?><p class="subtitle"><?= htmlspecialchars((string) $p['subtitle']) ?></p><?php endif; ?>
    <div class="public-doc-body"><?= (string) ($p['body_html'] ?? '') ?></div>
  </article>
</div>
