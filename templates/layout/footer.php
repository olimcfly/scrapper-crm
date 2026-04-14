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

      $bottomNav = [
        ['label' => 'Tableau de bord', 'icon' => '🏠', 'path' => '/dashboard'],
        ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects/sources'],
        ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/strategie'],
        ['label' => 'Messages', 'icon' => '💬', 'path' => '/messages-ia'],
        ['label' => 'Pipeline commercial', 'icon' => '📈', 'path' => '/pipeline'],
      ];

      require __DIR__ . '/../components/navigation/bottom_nav.php';
    ?>

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
