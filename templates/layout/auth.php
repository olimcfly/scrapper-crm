<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= htmlspecialchars($pageTitle ?? 'SCRAPPER CRM') ?></title>

  <!-- CSS GLOBAL -->
  <link rel="stylesheet" href="/assets/css/main.css">

  <!-- CSS AUTH -->
  <link rel="stylesheet" href="/assets/css/auth.css">

</head>

<body>

  <div class="auth-page">
    <?= $content ?? '' ?>
  </div>

</body>
</html>