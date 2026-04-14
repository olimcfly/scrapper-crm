<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>

  <link rel="stylesheet" href="/assets/css/main.css">

  <?php if (in_array($title ?? '', ['Connexion', 'Vérification'], true)): ?>
    <link rel="stylesheet" href="/assets/css/auth.css">
  <?php endif; ?>
</head>
<body>
<?php
$isLoginPage = in_array($title ?? '', ['Connexion', 'Vérification'], true);
$isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']);

$currentPath = isset($currentPath) && is_string($currentPath)
  ? $currentPath
  : (parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '');
?>

<?php if ($isLoginPage): ?>
  <div class="login-container">
    <main class="auth-page">
<?php else: ?>
  <div class="app-shell">
    <?php if ($isAuthenticated): ?>
      <?php require __DIR__ . '/../components/navigation/desktop_sidebar.php'; ?>
    <?php endif; ?>

    <div class="app-main">
      <?php if ($isAuthenticated): ?>
        <?php require __DIR__ . '/../components/navigation/topbar_compact.php'; ?>
      <?php endif; ?>

      <main class="page-content">
<?php endif; ?>
