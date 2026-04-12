<?php $isLoginPage = ($title ?? '') === 'Connexion'; ?>
<?php if ($isLoginPage): ?>
  </div>
<?php else: ?>
      </div>
    </main>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <?php $currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: ''; ?>
    <nav class="bottom-nav" aria-label="Navigation mobile principale">
      <?php
        $bottomNav = [
          ['label' => 'Dashboard', 'icon' => '🏠', 'path' => '/admin/dashboard'],
          ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects'],
          ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/admin/modules/strategie-prospect'],
          ['label' => 'Messages', 'icon' => '💬', 'path' => '/admin/modules/messages-ia'],
          ['label' => 'Pipeline', 'icon' => '📈', 'path' => '/admin/modules/pipeline'],
        ];
      ?>
      <?php foreach ($bottomNav as $item): ?>
        <a
          class="bottom-nav-link <?= ($currentPath === $item['path']) ? 'active' : '' ?>"
          href="<?= htmlspecialchars((string) $item['path']) ?>"
        >
          <span aria-hidden="true"><?= htmlspecialchars((string) $item['icon']) ?></span>
          <span><?= htmlspecialchars((string) $item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>
