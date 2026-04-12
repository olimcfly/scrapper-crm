<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    body{font-family:Arial,sans-serif;margin:0;background:#f6f8fb;color:#1f2937}
    .container{max-width:1000px;margin:0 auto;padding:20px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:16px}
    .row{display:flex;gap:10px;flex-wrap:wrap}.row>*{flex:1}
    input,select,textarea{width:100%;padding:8px;border:1px solid #d1d5db;border-radius:8px}
    table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #eef2f7;text-align:left}
    .btn{display:inline-block;padding:8px 12px;background:#2563eb;color:#fff;border:none;border-radius:8px;text-decoration:none;cursor:pointer}
    .btn.secondary{background:#6b7280}.btn.danger{background:#dc2626}
    .errors{background:#fef2f2;color:#991b1b;padding:10px;border-radius:8px;margin-bottom:10px}
  </style>
</head>
<body>
<div class="container">
  <h1>CRM PHP / MySQL</h1>
  <p><a href="/prospects">Prospects</a> · <a href="/prospects/create">Nouveau prospect</a></p>
