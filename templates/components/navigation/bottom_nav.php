<nav class="bottom-nav" aria-label="Navigation mobile principale">
  <div class="bottom-nav-inner">
    <?php foreach (($bottomNav ?? []) as $item): ?>
      <?php
        $path = (string) ($item['path'] ?? '');
        $isActive = $path !== '' && (
          $currentPath === $path ||
          str_starts_with($currentPath, rtrim($path, '/') . '/')
        );
      ?>
      <a
        class="bottom-nav-link <?= $isActive ? 'active' : '' ?>"
        href="<?= htmlspecialchars($path) ?>"
        <?= $isActive ? 'aria-current="page"' : '' ?>
      >
        <span class="bottom-nav-icon" aria-hidden="true"><?= htmlspecialchars((string) ($item['icon'] ?? '•')) ?></span>
        <span class="bottom-nav-label"><?= htmlspecialchars((string) ($item['label'] ?? 'Lien')) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</nav>
