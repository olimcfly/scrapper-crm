<?php if (!empty($isLoginPage)): ?>
    </main>
  </div>
<?php else: ?>
      </main>
    </div>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <?php
      $currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
      $mobileSidebarItems = [
        ['label' => 'Tableau de bord', 'icon' => '🏠', 'path' => '/dashboard'],
        ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects/sources'],
        ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/strategie'],
        ['label' => 'Messages', 'icon' => '💬', 'path' => '/messages-ia'],
        ['label' => 'Pipeline commercial', 'icon' => '📈', 'path' => '/pipeline'],
        ['label' => 'Paramètres', 'icon' => '⚙️', 'path' => '/settings'],
      ];
    ?>

    <div class="mobile-sidebar-backdrop" data-mobile-sidebar-backdrop hidden></div>
    <aside class="mobile-sidebar-drawer" id="mobile-sidebar-drawer" data-mobile-sidebar-drawer aria-hidden="true">
      <div class="mobile-sidebar-header">
        <h2 class="mobile-sidebar-title">Navigation</h2>
        <button type="button" class="mobile-sidebar-close" data-close-mobile-sidebar aria-label="Fermer le menu">✕</button>
      </div>

      <p class="sidebar-section-title">Navigation</p>
      <nav class="sidebar-nav" aria-label="Navigation mobile principale">
        <?php foreach ($mobileSidebarItems as $item): ?>
          <?php
            $path = (string) ($item['path'] ?? '');
            $isActive = $path !== '' && (
              $currentPath === $path ||
              str_starts_with($currentPath, rtrim($path, '/') . '/')
            );
          ?>
          <a class="sidebar-link <?= $isActive ? 'active' : '' ?>" href="<?= htmlspecialchars($path) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
            <div class="sidebar-link-left">
              <span class="icon"><?= htmlspecialchars((string) ($item['icon'] ?? '•')) ?></span>
              <span><?= htmlspecialchars((string) ($item['label'] ?? 'Lien')) ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-footer">
        <div class="user">
          <div class="avatar">
            <?= htmlspecialchars(strtoupper(substr((string) ($authUser['first_name'] ?? 'U'), 0, 1))) ?>
          </div>
          <div>
            <strong><?= htmlspecialchars((string) ($authUser['first_name'] ?? 'Utilisateur')) ?></strong>
            <div class="muted">Session active</div>
          </div>
        </div>
      </div>
    </aside>

    <button class="global-fab" type="button" data-open-mobile-actions aria-controls="mobile-action-sheet" aria-expanded="false" aria-label="Ouvrir les actions prospects">
      <span aria-hidden="true">＋</span>
      <span>Lancer une collecte</span>
    </button>

    <div class="mobile-action-sheet-backdrop" data-mobile-actions-backdrop hidden></div>
    <div class="mobile-action-sheet" id="mobile-action-sheet" data-mobile-actions-sheet aria-hidden="true">
      <div class="mobile-action-sheet-handle" aria-hidden="true"></div>
      <div class="mobile-action-sheet-header">
        <strong>Choisir une action</strong>
        <button type="button" class="mobile-action-sheet-close" data-close-mobile-actions aria-label="Fermer">✕</button>
      </div>
      <a class="mobile-action-item is-primary" href="/prospects/sources">Lancer une collecte</a>
      <a class="mobile-action-item" href="/prospects/create">Ajouter manuellement</a>
      <a class="mobile-action-item" href="/prospects/import">Importer un fichier</a>
    </div>
  <?php endif; ?>
<?php endif; ?>

<script>
(() => {
  const sidebarOpenBtn = document.querySelector('[data-open-mobile-sidebar]');
  const sidebarCloseBtn = document.querySelector('[data-close-mobile-sidebar]');
  const sidebarBackdrop = document.querySelector('[data-mobile-sidebar-backdrop]');
  const sidebarDrawer = document.querySelector('[data-mobile-sidebar-drawer]');

  if (sidebarOpenBtn && sidebarBackdrop && sidebarDrawer) {
    const toggleSidebar = (open) => {
      sidebarOpenBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      sidebarDrawer.setAttribute('aria-hidden', open ? 'false' : 'true');
      sidebarBackdrop.hidden = !open;
      document.body.classList.toggle('mobile-sidebar-open', open);
    };

    sidebarOpenBtn.addEventListener('click', () => toggleSidebar(true));
    sidebarCloseBtn?.addEventListener('click', () => toggleSidebar(false));
    sidebarBackdrop.addEventListener('click', () => toggleSidebar(false));
  }

  const openBtn = document.querySelector('[data-open-mobile-actions]');
  const closeBtn = document.querySelector('[data-close-mobile-actions]');
  const backdrop = document.querySelector('[data-mobile-actions-backdrop]');
  const sheet = document.querySelector('[data-mobile-actions-sheet]');

  if (!openBtn || !backdrop || !sheet) {
    return;
  }

  const toggle = (open) => {
    openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    sheet.setAttribute('aria-hidden', open ? 'false' : 'true');
    backdrop.hidden = !open;
    document.body.classList.toggle('mobile-action-sheet-open', open);
  };

  openBtn.addEventListener('click', () => toggle(true));
  closeBtn?.addEventListener('click', () => toggle(false));
  backdrop.addEventListener('click', () => toggle(false));
})();
</script>

</body>
</html>
