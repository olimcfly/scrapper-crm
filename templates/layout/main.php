<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>

  <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<?php
$isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']);
?>

<div class="app-shell">
  <?php if ($isAuthenticated): ?>
    <?php require __DIR__ . '/../components/navigation/desktop_sidebar.php'; ?>
  <?php endif; ?>

  <div class="app-main">
    <?php if ($isAuthenticated): ?>
      <?php require __DIR__ . '/../components/navigation/topbar_compact.php'; ?>
    <?php endif; ?>

    <main class="page-content">
      <?= $content ?? '' ?>
    </main>
  </div>
</div>

</body>
</html>
