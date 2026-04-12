<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    :root {
      --color-bg: #f6f8fb;
      --color-text: #1f2937;
      --color-white: #ffffff;
      --color-border: #e5e7eb;
      --color-primary: #2563eb;
      --color-secondary: #6b7280;
      --color-danger: #dc2626;
      --font-sans: "Inter", "Segoe UI", Arial, sans-serif;
      --font-serif: "Cormorant Garamond", "Georgia", serif;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--color-bg);
      color: var(--color-text);
      font-family: var(--font-sans);
    }

    .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
    .card { background: var(--color-white); border: 1px solid var(--color-border); border-radius: 10px; padding: 16px; margin-bottom: 16px; }
    .row { display: flex; gap: 10px; flex-wrap: wrap; }
    .row > * { flex: 1; }
    input, select, textarea { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #eef2f7; text-align: left; }
    .btn { display: inline-block; padding: 8px 12px; background: var(--color-primary); color: #fff; border: none; border-radius: 8px; text-decoration: none; cursor: pointer; }
    .btn.secondary { background: var(--color-secondary); }
    .btn.danger { background: var(--color-danger); }
    .errors { background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
  </style>
</head>
<body>
<?php $isLoginPage = ($title ?? '') === 'Connexion'; ?>
<?php if (!$isLoginPage): ?>
<div class="container">
  <h1>CRM PHP / MySQL</h1>
  <?php $isAuthenticated = isset($_SESSION['auth_user']['id']); ?>
  <p>
    <?php if ($isAuthenticated): ?>
      <a href="/prospects">Prospects</a> · <a href="/prospects/create">Nouveau prospect</a>
      <form method="post" action="/logout" style="display:inline;margin-left:10px;">
        <button type="submit" class="btn secondary" style="padding:4px 8px;">Déconnexion</button>
      </form>
    <?php else: ?>
      <a href="/login">Connexion</a>
    <?php endif; ?>
  </p>
<?php endif; ?>
