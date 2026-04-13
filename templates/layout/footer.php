<?php if (!empty($isLoginPage)): ?>

  </main>
  </div>

<?php else: ?>

      <!-- FIN PAGE WRAPPER -->
      </div>

    </main>
  </div>

  <!-- BOTTOM NAV MOBILE -->
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
  <?php endif; ?>

<?php endif; ?>

</body>
</html>