<?php $isLoginPage = ($title ?? '') === 'Connexion'; ?>
<?php if ($isLoginPage): ?>
  </div>
<?php else: ?>
      </div>
    </main>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <?php
      $currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
      $bottomNavItems = [
        ['label' => 'Dashboard', 'icon' => '🏠', 'path' => '/admin/dashboard'],
        ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects'],
        ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/strategie'],
        ['label' => 'Messages IA', 'icon' => '💬', 'path' => '/messages-ia'],
        ['label' => 'Pipeline', 'icon' => '📈', 'path' => '/pipeline'],
      ];
      include __DIR__ . '/../components/BottomNav.php';
    ?>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>
