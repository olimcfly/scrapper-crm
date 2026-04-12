<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    :root {
      --color-bg: #f4f6fb;
      --color-text: #0f172a;
      --color-white: #ffffff;
      --color-border: #e2e8f0;
      --color-primary: #3b82f6;
      --color-secondary: #475569;
      --color-danger: #dc2626;
      --color-sidebar: #0b1220;
      --font-sans: "Inter", "Segoe UI", Arial, sans-serif;
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: var(--color-bg); color: var(--color-text); font-family: var(--font-sans); }
    .app-shell { display: flex; min-height: 100vh; }
    .sidebar { width: 320px; background: var(--color-sidebar); color: #dbeafe; padding: 20px 16px; }
    .brand { font-size: 18px; font-weight: 700; color: white; margin-bottom: 20px; }
    .sidebar-section-title { margin: 18px 0 8px; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #93c5fd; }
    .module-link { display: block; padding: 10px 10px; border-radius: 10px; color: #cbd5e1; text-decoration: none; margin-bottom: 6px; border: 1px solid transparent; }
    .module-link:hover { background: rgba(59,130,246,.12); color: #fff; }
    .module-link.active { background: rgba(59,130,246,.25); border-color: rgba(148,163,184,.35); color: #fff; }
    .module-link .label-row { display:flex; justify-content:space-between; align-items:center; gap:8px; }
    .module-link small { color:#94a3b8; display:block; margin-top:2px; }
    .workspace { flex: 1; padding: 28px; }
    .workspace-header { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:18px; }
    .container { max-width: 1240px; margin: 0 auto; }
    .card { background: var(--color-white); border: 1px solid var(--color-border); border-radius: 14px; padding: 18px; margin-bottom: 16px; }
    .premium-hero { background: linear-gradient(130deg, #0f172a 0%, #1e3a8a 55%, #2563eb 100%); color: #e0ecff; border: none; }
    .premium-hero h2 { margin:0 0 8px; color: #ffffff; }
    .eyebrow { text-transform: uppercase; letter-spacing: .08em; font-size: 12px; margin:0 0 6px; color:#bfdbfe; }
    .row { display: flex; gap: 12px; flex-wrap: wrap; }
    .row > * { flex: 1; min-width: 180px; }
    .metrics-row .metric-card { min-width: 140px; }
    .metric-title { margin:0; color:#64748b; }
    .metric-value { margin:4px 0 0; font-size:28px; font-weight:700; }
    .module-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px; }
    .module-card { border: 1px solid var(--color-border); border-radius: 12px; padding: 14px; background:#fff; }
    .core-priority { border-color:#93c5fd; box-shadow: inset 0 0 0 1px #dbeafe; }
    .module-card-head { display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:10px; }
    .module-card p { margin-top:0; color:#475569; min-height: 42px; }
    input, select, textarea { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #eef2f7; text-align: left; }
    .btn { display: inline-block; padding: 8px 12px; background: var(--primary); color: #fff; border: none; border-radius: 8px; text-decoration: none; cursor: pointer; }
    .btn.secondary { background: #475569; }
    .btn.danger { background: var(--danger); }
    .errors { background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
    .muted { color:#64748b; }
    .status-badge { display:inline-block; padding: 2px 9px; border-radius:999px; font-size:11px; font-weight:700; white-space: nowrap; }
    .status-active { background:#dcfce7; color:#166534; border:1px solid #86efac; }
    .status-beta { background:#fef9c3; color:#854d0e; border:1px solid #fde047; }
    .status-dev { background:#e0e7ff; color:#3730a3; border:1px solid #a5b4fc; }
    .top-actions { display:flex; align-items:center; gap:10px; }

    @media (max-width: 1100px) {
      .app-shell { flex-direction: column; }
      .sidebar { width: 100%; }
    }
  </style>
</head>
<body>
<?php
  $isLoginPage = ($title ?? '') === 'Connexion';
  $isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']);
  $currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
  $modules = \App\Config\AdminModules::all();
  $statusLabels = \App\Config\AdminModules::statusLabels();
  $statusClassMap = \App\Config\AdminModules::statusClassMap();
?>
<?php if ($isLoginPage): ?>
  <div class="container" style="max-width:760px;padding:30px 20px;">
<?php else: ?>
  <div class="app-shell">
    <?php if ($isAuthenticated): ?>
      <aside class="sidebar">
        <div class="brand">SCRAPPER CRM Admin</div>
        <div class="sidebar-section-title">Modules produit</div>
        <?php foreach ($modules as $module): ?>
          <a class="module-link <?= ($currentPath === $module['path']) ? 'active' : '' ?>" href="<?= htmlspecialchars((string) $module['path']) ?>">
            <span class="label-row">
              <span><?= htmlspecialchars((string) $module['label']) ?></span>
              <span class="status-badge <?= htmlspecialchars((string) ($statusClassMap[$module['status']] ?? '')) ?>">
                <?= htmlspecialchars((string) ($statusLabels[$module['status']] ?? $module['status'])) ?>
              </span>
            </span>
            <small><?= htmlspecialchars((string) $module['description']) ?></small>
          </a>
        <?php endforeach; ?>
      </aside>
    <?php endif; ?>
    <main class="workspace">
      <div class="container">
        <div class="workspace-header">
          <h1 style="margin:0;"><?= htmlspecialchars((string) ($title ?? 'CRM')) ?></h1>
          <?php if ($isAuthenticated): ?>
            <div class="top-actions">
              <span style="display:inline-block;padding:4px 10px;background:#eef2ff;border:1px solid #c7d2fe;border-radius:999px;color:#3730a3;font-size:13px;">
                <?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?> · <?= htmlspecialchars((string) ($authUser['email'] ?? '')) ?>
              </span>
              <form method="post" action="/logout" style="display:inline;">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                <button type="submit" class="btn secondary" style="padding:4px 8px;">Déconnexion</button>
              </form>
            </div>
          <?php endif; ?>
        </div>
<?php endif; ?>
