<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    body{font-family:Arial,sans-serif;margin:0;background:#f6f8fb;color:#1f2937;line-height:1.4}
    .container{max-width:1000px;margin:0 auto;padding:20px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:16px}
    .row{display:flex;gap:10px;flex-wrap:wrap}.row>*{flex:1;min-width:220px}
    input,select,textarea{width:100%;padding:8px;border:1px solid #d1d5db;border-radius:8px;box-sizing:border-box}
    textarea{min-height:100px}
    table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #eef2f7;text-align:left;vertical-align:top}
    th{background:#f9fafb}
    .btn{display:inline-block;padding:8px 12px;background:#2563eb;color:#fff;border:none;border-radius:8px;text-decoration:none;cursor:pointer}
    .btn.secondary{background:#6b7280}.btn.danger{background:#dc2626}
    .errors{background:#fef2f2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px}
    .alert{padding:10px;border-radius:8px;margin:0 0 16px 0}
    .alert.success{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
    .alert.warning{background:#fffbeb;color:#92400e;border:1px solid #fde68a}
    .muted{color:#6b7280}
    .pagination{display:flex;gap:10px;margin-top:12px}
    .timeline-item{padding:10px 0;border-bottom:1px solid #eef2f7}
    .timeline-item:last-child{border-bottom:none}
  </style>
</head>
<body>
<div class="container">
  <h1>CRM PHP / MySQL</h1>
  <?php $isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']); ?>
  <p>
    <?php if ($isAuthenticated): ?>
      <a href="/prospects">Prospects</a> · <a href="/prospects/create">Nouveau prospect</a> · <a href="/settings">Paramètres</a>
      <span style="display:inline-block;margin-left:12px;padding:4px 10px;background:#eef2ff;border:1px solid #c7d2fe;border-radius:999px;color:#3730a3;font-size:13px;">
        <?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?> · <?= htmlspecialchars((string) ($authUser['email'] ?? '')) ?>
      </span>
      <form method="post" action="/logout" style="display:inline;margin-left:10px;">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
        <button type="submit" class="btn secondary" style="padding:4px 8px;">Déconnexion</button>
      </form>
    <?php else: ?>
      <a href="/login">Connexion</a>
    <?php endif; ?>
  </p>
