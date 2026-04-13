<nav class="bottom-nav" aria-label="Navigation mobile principale">
  <?php foreach (($bottomNav ?? []) as $item): ?>
    <a class="bottom-nav-link <?= ($currentPath === $item['path']) ? 'active' : '' ?>" href="<?= htmlspecialchars((string) $item['path']) ?>">
      <span aria-hidden="true"><?= htmlspecialchars((string) $item['icon']) ?></span>
      <span><?= htmlspecialchars((string) $item['label']) ?></span>
    </a>
  <?php endforeach; ?>
</nav>