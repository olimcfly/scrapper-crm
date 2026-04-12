<?php

declare(strict_types=1);
?>
<aside class="sidebar desktop-sidebar" aria-label="Navigation principale desktop">
  <div class="brand">SCRAPPER CRM</div>
  <p class="sidebar-section-title">MVP mobile-first</p>
  <?php foreach (($modules ?? []) as $module): ?>
    <a class="module-link <?= ($currentPath === $module['path']) ? 'active' : '' ?>" href="<?= htmlspecialchars((string) $module['path']) ?>">
      <span class="label-row">
        <span><?= htmlspecialchars((string) $module['label']) ?></span>
        <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status']] ?? '')) ?>">
          <?= htmlspecialchars((string) ($statusLabels[$module['status']] ?? $module['status'])) ?>
        </span>
      </span>
      <small><?= htmlspecialchars((string) $module['description']) ?></small>
    </a>
  <?php endforeach; ?>
</aside>
