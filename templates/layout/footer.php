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
        ['label' => 'Dashboard', 'icon' => '🏠', 'path' => '/dashboard'],
        ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects'],
        ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/strategie'],
        ['label' => 'Messages', 'icon' => '💬', 'path' => '/messages-ia'],
        ['label' => 'Pipeline', 'icon' => '📈', 'path' => '/pipeline'],
      ];

      require __DIR__ . '/../components/navigation/bottom_nav.php';
    ?>

    <a class="global-fab" href="/prospects/create" aria-label="Créer un prospect ou lancer une action">
      <span aria-hidden="true">＋</span>
      <span>Action</span>
    </a>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
