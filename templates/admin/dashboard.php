<div class="card premium-hero">
  <p class="eyebrow">Admin premium</p>
  <h2>Architecture produit complète</h2>
  <p class="muted" style="max-width:860px;">
    Cette vue expose toute la structure de l'application (14 modules) pour piloter la roadmap sans casser les routes existantes.
  </p>
</div>

<div class="row metrics-row">
  <div class="card metric-card">
    <p class="metric-title">Actifs</p>
    <p class="metric-value"><?= (int) ($statusCounters['active'] ?? 0) ?></p>
  </div>
  <div class="card metric-card">
    <p class="metric-title">Bêta</p>
    <p class="metric-value"><?= (int) ($statusCounters['beta'] ?? 0) ?></p>
  </div>
  <div class="card metric-card">
    <p class="metric-title">En développement</p>
    <p class="metric-value"><?= (int) ($statusCounters['in_progress'] ?? 0) ?></p>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Cœur produit prioritaire</h3>
  <div class="module-grid core-grid">
    <?php foreach ($coreModules as $module): ?>
      <article class="module-card core-priority">
        <div class="module-card-head">
          <strong><?= htmlspecialchars((string) $module['label']) ?></strong>
          <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status']] ?? '')) ?>">
            <?= htmlspecialchars((string) ($statusLabels[$module['status']] ?? $module['status'])) ?>
          </span>
        </div>
        <p><?= htmlspecialchars((string) $module['description']) ?></p>
        <a class="btn secondary" href="<?= htmlspecialchars((string) $module['path']) ?>">Ouvrir</a>
      </article>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Cartographie des modules</h3>
  <div class="module-grid">
    <?php foreach ($modules as $module): ?>
      <article class="module-card">
        <div class="module-card-head">
          <strong><?= htmlspecialchars((string) $module['label']) ?></strong>
          <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status']] ?? '')) ?>">
            <?= htmlspecialchars((string) ($statusLabels[$module['status']] ?? $module['status'])) ?>
          </span>
        </div>
        <p><?= htmlspecialchars((string) $module['description']) ?></p>
        <a class="btn secondary" href="<?= htmlspecialchars((string) $module['path']) ?>">Accéder</a>
      </article>
    <?php endforeach; ?>
  </div>
</div>
