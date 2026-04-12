<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    :root {
      --ds-color-bg: #f8fafc;
      --ds-color-surface: #ffffff;
      --ds-color-surface-soft: #eef2ff;
      --ds-color-text: #0f172a;
      --ds-color-text-muted: #64748b;
      --ds-color-border: #e2e8f0;
      --ds-color-primary: #2563eb;
      --ds-color-primary-strong: #1d4ed8;
      --ds-color-success: #16a34a;
      --ds-color-warning: #ca8a04;
      --ds-color-danger: #dc2626;
      --ds-color-sidebar: #0b1220;
      --ds-space-1: 4px;
      --ds-space-2: 8px;
      --ds-space-3: 12px;
      --ds-space-4: 16px;
      --ds-space-5: 24px;
      --ds-space-6: 32px;
      --ds-radius-sm: 8px;
      --ds-radius-md: 12px;
      --ds-radius-lg: 16px;
      --ds-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
      --ds-shadow-md: 0 8px 24px rgba(15, 23, 42, 0.08);
      --ds-font-sans: "Inter", "Segoe UI", Arial, sans-serif;
      --bottom-nav-height: 72px;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background: var(--ds-color-bg);
      color: var(--ds-color-text);
      font-family: var(--ds-font-sans);
      line-height: 1.4;
    }
    .app-shell { min-height: 100vh; display: grid; grid-template-columns: 1fr; }
    .sidebar {
      display: none;
      width: 280px;
      background: var(--ds-color-sidebar);
      color: #dbeafe;
      padding: var(--ds-space-5) var(--ds-space-4);
      border-right: 1px solid rgba(148, 163, 184, 0.2);
      position: sticky;
      top: 0;
      height: 100vh;
      overflow: auto;
    }
    .brand { font-size: 18px; font-weight: 700; color: white; margin-bottom: var(--ds-space-5); }
    .sidebar-section-title { margin: 0 0 var(--ds-space-3); font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: #93c5fd; }
    .module-link { display: block; padding: 10px; border-radius: var(--ds-radius-sm); color: #cbd5e1; text-decoration: none; margin-bottom: 6px; border: 1px solid transparent; }
    .module-link:hover, .module-link:focus-visible { background: rgba(59, 130, 246, .14); color: #fff; outline: none; }
    .module-link.active { background: rgba(59, 130, 246, .3); border-color: rgba(148, 163, 184, .35); color: #fff; }
    .module-link .label-row { display: flex; justify-content: space-between; align-items: center; gap: var(--ds-space-2); }
    .module-link small { color: #94a3b8; display: block; margin-top: 2px; }
    .workspace { width: 100%; padding: var(--ds-space-4); padding-bottom: calc(var(--bottom-nav-height) + var(--ds-space-5)); }
    .container { max-width: 1240px; margin: 0 auto; }
    .topbar {
      position: sticky;
      top: 0;
      z-index: 30;
      margin-bottom: var(--ds-space-4);
      background: rgba(248, 250, 252, 0.96);
      backdrop-filter: blur(8px);
      border: 1px solid var(--ds-color-border);
      border-radius: var(--ds-radius-md);
      padding: var(--ds-space-3) var(--ds-space-4);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: var(--ds-space-3);
      box-shadow: var(--ds-shadow-sm);
    }
    .topbar-main h1 { margin: 0; font-size: 18px; }
    .topbar-main .muted { margin-top: 2px; }
    .topbar-actions { display: flex; align-items: center; gap: var(--ds-space-2); flex-wrap: wrap; justify-content: flex-end; }
    .search-input { max-width: 220px; width: 100%; border: 1px solid var(--ds-color-border); border-radius: 999px; padding: 8px 12px; background: #fff; }
    .user-chip { display:inline-block; padding:6px 10px; background:var(--ds-color-surface-soft); border:1px solid #c7d2fe; border-radius:999px; color:#3730a3; font-size:13px; }
    .card { background: var(--ds-color-surface); border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-md); box-shadow: var(--ds-shadow-sm); padding: var(--ds-space-4); margin-bottom: var(--ds-space-4); }
    .row { display: flex; gap: var(--ds-space-3); flex-wrap: wrap; }
    .row > * { flex: 1; min-width: 180px; }
    .muted { color: var(--ds-color-text-muted); }
    .btn { display: inline-flex; align-items: center; justify-content: center; min-width: 44px; min-height: 44px; padding: 10px 12px; border: none; border-radius: var(--ds-radius-sm); background: var(--ds-color-primary); color: #fff; text-decoration: none; cursor: pointer; font-weight: 600; }
    .btn:hover, .btn:focus-visible { background: var(--ds-color-primary-strong); outline: none; }
    .btn.secondary { background: #475569; }
    .btn.compact { min-height: 36px; padding: 6px 10px; }
    .primary-cta-button { min-height: 48px; border-radius: 12px; }
    input, select, textarea { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: var(--ds-radius-sm); background: #fff; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #eef2f7; text-align: left; }
    .status-badge { display: inline-block; padding: 2px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .status-active { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-mvp { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .status-placeholder { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }
    .global-state { border-radius: var(--ds-radius-md); border: 1px solid var(--ds-color-border); padding: var(--ds-space-4); display: flex; align-items: center; gap: var(--ds-space-3); margin-bottom: var(--ds-space-3); background: #fff; }
    .global-state.loading { border-color: #bfdbfe; background: #eff6ff; }
    .global-state.empty { border-color: #cbd5e1; background: #f8fafc; }
    .global-state.error { border-color: #fecaca; background: #fef2f2; }
    .state-dot { width: 12px; height: 12px; border-radius: 999px; flex-shrink: 0; background: currentColor; }
    .global-state.loading .state-dot { color: var(--ds-color-primary); }
    .global-state.empty .state-dot { color: #64748b; }
    .global-state.error .state-dot { color: var(--ds-color-danger); }
    .loading-skeleton-card .skeleton-line { height: 12px; border-radius: 999px; background: linear-gradient(90deg,#e2e8f0 0%,#f1f5f9 45%,#e2e8f0 100%); background-size: 200% 100%; margin-bottom: 10px; animation: pulse 1.4s infinite; }
    .loading-skeleton-card .w-40 { width: 40%; }
    .loading-skeleton-card .w-60 { width: 60%; }
    .loading-skeleton-card .w-80 { width: 80%; }
    .empty-state-guided { border-style: dashed; }
    .empty-state-guided .empty-eyebrow { text-transform: uppercase; letter-spacing: .08em; font-size: 12px; color: #475569; margin-top: 0; }
    .dashboard-kpi { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: var(--ds-space-3); }
    .kpi-card { border: 1px solid var(--ds-color-border); border-radius: var(--ds-radius-sm); padding: var(--ds-space-3); background: #fff; }
    .kpi-value { font-size: 24px; font-weight: 700; margin: 2px 0 0; }
    .quick-actions { display: grid; grid-template-columns: 1fr; gap: var(--ds-space-2); }
    .bottom-nav {
      position: fixed; left: 0; right: 0; bottom: 0; height: var(--bottom-nav-height); background: #fff;
      border-top: 1px solid var(--ds-color-border); box-shadow: 0 -8px 20px rgba(15, 23, 42, 0.08);
      display: grid; grid-template-columns: repeat(5, 1fr); z-index: 40;
    }
    .bottom-nav-link { text-decoration: none; color: #475569; font-size: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; padding: 6px; }
    .bottom-nav-link.active { color: var(--ds-color-primary-strong); font-weight: 700; }
    .bottom-nav-link span[aria-hidden='true'] { font-size: 18px; }
    @keyframes pulse { 0% { background-position: 0 0; } 100% { background-position: 200% 0; } }
    @media (min-width: 600px) { .quick-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 900px) {
      .app-shell { grid-template-columns: 280px 1fr; }
      .sidebar { display: block; }
      .workspace { padding: var(--ds-space-5); }
      .bottom-nav { display: none; }
      .topbar-main h1 { font-size: 20px; }
      .dashboard-kpi { grid-template-columns: repeat(4, minmax(0, 1fr)); }
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
$bottomNav = [
    ['label' => 'Dashboard', 'icon' => '🏠', 'path' => '/admin/dashboard'],
    ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects'],
    ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/admin/modules/strategie-prospect'],
    ['label' => 'Messages', 'icon' => '💬', 'path' => '/admin/modules/messages-ia'],
    ['label' => 'Pipeline', 'icon' => '📈', 'path' => '/admin/modules/pipeline'],
];
?>
<?php if ($isLoginPage): ?>
  <div class="container" style="max-width:760px;padding:30px 20px;">
<?php else: ?>
  <div class="app-shell">
    <?php if ($isAuthenticated): ?>
      <?php require __DIR__ . '/../components/desktop_sidebar.php'; ?>
    <?php endif; ?>
    <main class="workspace">
      <div class="container">
        <?php require __DIR__ . '/../components/topbar_compact.php'; ?>
<?php endif; ?>
