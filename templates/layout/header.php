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
      --ds-color-warning: #d97706;
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
      --bottom-nav-height: 74px;
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
    .workspace {
      width: 100%;
      padding: var(--ds-space-4);
      padding-bottom: calc(var(--bottom-nav-height) + var(--ds-space-5));
    }
    .container { max-width: 1240px; margin: 0 auto; }

    .sidebar {
      display: none;
      width: 280px;
      background: var(--ds-color-sidebar);
      color: #dbeafe;
      padding: var(--ds-space-5) var(--ds-space-4);
      border-right: 1px solid rgba(148, 163, 184, 0.2);
    }
    .brand { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: var(--ds-space-5); }
    .sidebar-section-title {
      margin: 0 0 var(--ds-space-3);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #93c5fd;
    }

    .module-link {
      display: block;
      padding: 10px;
      border-radius: var(--ds-radius-sm);
      color: #cbd5e1;
      text-decoration: none;
      margin-bottom: 6px;
      border: 1px solid transparent;
    }
    .module-link:hover,
    .module-link:focus-visible { background: rgba(59, 130, 246, .14); color: #fff; outline: none; }
    .module-link.active { background: rgba(59, 130, 246, .3); border-color: rgba(148, 163, 184, .35); color: #fff; }
    .module-link .label-row { display: flex; justify-content: space-between; align-items: center; gap: var(--ds-space-2); }
    .module-link small { color: #94a3b8; display: block; margin-top: 2px; }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 30;
      margin-bottom: var(--ds-space-4);
      background: rgba(248, 250, 252, 0.96);
      border: 1px solid var(--ds-color-border);
      border-radius: var(--ds-radius-md);
      padding: var(--ds-space-3) var(--ds-space-4);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: var(--ds-space-3);
      box-shadow: var(--ds-shadow-sm);
      backdrop-filter: blur(8px);
    }
    .topbar-main h1 { margin: 0; font-size: 18px; }
    .eyebrow {
      text-transform: uppercase;
      letter-spacing: .08em;
      font-size: 11px;
      color: var(--ds-color-text-muted);
      margin: 0 0 4px;
      font-weight: 700;
    }
    .topbar-actions { display: flex; align-items: center; gap: var(--ds-space-2); }
    .search-input {
      max-width: 220px;
      border: 1px solid var(--ds-color-border);
      border-radius: 999px;
      padding: 8px 12px;
      background: #fff;
    }
    .user-chip {
      display: inline-block;
      padding: 6px 10px;
      background: var(--ds-color-surface-soft);
      border: 1px solid #c7d2fe;
      border-radius: 999px;
      color: #3730a3;
      font-size: 13px;
    }

    .card {
      background: var(--ds-color-surface);
      border: 1px solid var(--ds-color-border);
      border-radius: var(--ds-radius-md);
      box-shadow: var(--ds-shadow-sm);
      padding: var(--ds-space-4);
      margin-bottom: var(--ds-space-4);
    }
    .row { display: flex; gap: var(--ds-space-3); flex-wrap: wrap; }
    .row > * { flex: 1; min-width: 180px; }
    .muted { color: var(--ds-color-text-muted); }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-width: 44px;
      min-height: 44px;
      padding: 10px 12px;
      border: none;
      border-radius: var(--ds-radius-sm);
      background: var(--ds-color-primary);
      color: #fff;
      text-decoration: none;
      cursor: pointer;
      font-weight: 600;
    }
    .btn:hover,
    .btn:focus-visible { background: var(--ds-color-primary-strong); outline: none; }
    .btn.secondary { background: #475569; }
    .btn.danger { background: var(--ds-color-danger); }
    .btn-cta { min-height: 48px; padding: 12px 16px; }
    .btn-compact { min-height: 36px; padding: 6px 10px; }

    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #d1d5db;
      border-radius: var(--ds-radius-sm);
      background: #fff;
    }

    .status-badge {
      display: inline-block;
      padding: 2px 9px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      white-space: nowrap;
    }
    .status-active { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-mvp { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .status-placeholder { background: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }

    .global-state {
      border-radius: var(--ds-radius-md);
      border: 1px solid var(--ds-color-border);
      padding: var(--ds-space-4);
      display: flex;
      align-items: center;
      gap: var(--ds-space-3);
      margin-bottom: var(--ds-space-3);
      background: #fff;
    }
    .global-state.error { border-color: #fecaca; background: #fef2f2; }
    .state-dot { width: 12px; height: 12px; border-radius: 999px; flex-shrink: 0; background: var(--ds-color-danger); }

    .skeleton-card { animation: pulse 1.3s ease-in-out infinite; }
    .skeleton-line { height: 10px; border-radius: 999px; background: #e2e8f0; margin-bottom: 10px; }
    .skeleton-line-title { width: 48%; height: 14px; }
    .skeleton-line:last-child { margin-bottom: 0; width: 65%; }

    .empty-guided h3 { margin: 6px 0 8px; }

    .bottom-nav {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      height: var(--bottom-nav-height);
      background: #fff;
      border-top: 1px solid var(--ds-color-border);
      box-shadow: 0 -8px 20px rgba(15, 23, 42, 0.08);
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      z-index: 40;
      padding-bottom: env(safe-area-inset-bottom, 0);
    }
    .bottom-nav-link {
      text-decoration: none;
      color: #475569;
      font-size: 12px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
      min-height: 52px;
      padding: 8px 4px;
    }
    .bottom-nav-link.active { color: var(--ds-color-primary-strong); font-weight: 700; }
    .nav-icon { font-size: 18px; }

    @keyframes pulse {
      0%,100% { opacity: .65; }
      50% { opacity: 1; }
    }

    @media (max-width: 640px) {
      .topbar-actions .search-input,
      .topbar-actions .user-chip { display: none; }
    }

    @media (min-width: 900px) {
      .app-shell { grid-template-columns: 280px 1fr; }
      .sidebar { display: block; }
      .workspace { padding: var(--ds-space-5); }
      .bottom-nav { display: none; }
      .topbar-main h1 { font-size: 20px; }
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
  $bottomNavItems = [
    ['label' => 'Dashboard', 'icon' => '🏠', 'path' => '/admin/dashboard'],
    ['label' => 'Prospects', 'icon' => '👥', 'path' => '/prospects'],
    ['label' => 'Stratégie', 'icon' => '🎯', 'path' => '/strategie'],
    ['label' => 'Messages IA', 'icon' => '💬', 'path' => '/messages-ia'],
    ['label' => 'Pipeline', 'icon' => '📈', 'path' => '/pipeline'],
  ];
?>
<?php if ($isLoginPage): ?>
  <div class="container" style="max-width:760px;padding:30px 20px;">
<?php else: ?>
  <div class="app-shell">
    <?php if ($isAuthenticated): ?>
      <?php $desktopModules = $modules; include __DIR__ . '/../components/DesktopSidebar.php'; ?>
    <?php endif; ?>

    <main class="workspace">
      <div class="container">
        <?php include __DIR__ . '/../components/TopBarCompact.php'; ?>
<?php endif; ?>
