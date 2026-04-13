<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>

  <!-- CSS GLOBAL -->
  <link rel="stylesheet" href="/assets/css/main.css">

  <!-- CSS AUTH -->
  <?php if (in_array($title ?? '', ['Connexion', 'Vérification'], true)): ?>
    <link rel="stylesheet" href="/assets/css/auth.css">
  <?php endif; ?>

</head>

<body>

<?php
$isLoginPage = in_array($title ?? '', ['Connexion', 'Vérification'], true);
$isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']);
?>

<?php if ($isLoginPage): ?>

  <!-- LOGIN -->
  <div class="login-container">
    <main class="auth-page">
      <?= $content ?? '' ?>
    </main>
  </div>

<?php else: ?>

  <!-- APP -->
  <div class="app-shell">

    <!-- SIDEBAR -->
    <?php if ($isAuthenticated): ?>
      <?php require __DIR__ . '/../components/navigation/desktop_sidebar.php'; ?>
    <?php endif; ?>

    <!-- WORKSPACE -->
    <main class="workspace">

      <!-- TOPBAR -->
      <?php if ($isAuthenticated): ?>
        <?php require __DIR__ . '/../components/navigation/topbar_compact.php'; ?>
      <?php endif; ?>

      <!-- PAGE CONTENT -->
      <div class="page-wrapper">
        <?= $content ?? '' ?>
      </div>

    </main>

  </div>

<?php endif; ?>

</body>
</html>