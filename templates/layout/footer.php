<?php $isLoginPage = ($title ?? '') === 'Connexion'; ?>
<?php if ($isLoginPage): ?>
  </div>
<?php else: ?>
      </div>
    </main>
  </div>

  <?php if (is_array($authUser ?? null) && isset($authUser['id'])): ?>
    <?php require __DIR__ . '/../components/bottom_nav.php'; ?>
  <?php endif; ?>
<?php endif; ?>
</body>
</html>
