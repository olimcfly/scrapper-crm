<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    :root {
      --ds-color-bg: #f3f6fb;
      --ds-color-surface: #ffffff;
      --ds-color-surface-soft: #eef2ff;
      --ds-color-surface-alt: #f8fafc;
      --ds-color-text: #0f172a;
      --ds-color-text-muted: #64748b;
      --ds-color-border: #dde3ee;
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
      --ds-radius-sm: 10px;
      --ds-radius-md: 14px;
      --ds-radius-lg: 18px;
      --ds-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.06);
      --ds-shadow-md: 0 10px 28px rgba(15, 23, 42, 0.08);
      --ds-font-sans: "Inter", "Segoe UI", Arial, sans-serif;
      --bottom-nav-height: 74px;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--ds-color-bg);
      color: var(--ds-color-text);
      font-family: var(--ds-font-sans);
      line-height: 1.45;
      font-size: 15px;
    }

    h1, h2, h3 { letter-spacing: -0.01em; }
    p { margin: 0; }

    .app-shell { min-height: 100vh; display: grid; grid-template-columns: 1fr; }
    .sidebar {
      display: none;
      width: 280px;
      background: var(--ds-color-sidebar);
      color: #dbeafe;
      padding: var(--ds-space-5) var(--ds-space-4);
      border-right: 1px solid rgba(148, 163, 184, 0.2);
    }

    .brand { font-size: 18px; font-weight: 700; color: white; margin-bottom: var(--ds-space-5); }
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

    .module-link:hover, .module-link:focus-visible { background: rgba(59, 130, 246, .14); color: #fff; outline: none; }
    .module-link.active { background: rgba(59, 130, 246, .3); border-color: rgba(148, 163, 184, .35); color: #fff; }

    .module-link .label-row { display: flex; justify-content: space-between; align-items: center; gap: var(--ds-space-2); }
    .module-link small { color: #94a3b8; display: block; margin-top: 2px; }

    .workspace {
      width: 100%;
      padding: var(--ds-space-4);
      padding-bottom: calc(var(--bottom-nav-height) + var(--ds-space-5));
    }

    .container { max-width: 1240px; margin: 0 auto; }
    .container.login-container { max-width: 1200px; padding: 0; }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 30;
      margin-bottom: var(--ds-space-4);
      background: rgba(255, 255, 255, 0.93);
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

    .topbar-main h1 { margin: 0; font-size: 20px; }
    .topbar-subtitle { margin-top: 2px; color: var(--ds-color-text-muted); font-size: 13px; }
    .topbar-actions { display: flex; align-items: center; gap: var(--ds-space-2); flex-wrap: wrap; justify-content: flex-end; }

    .search-input {
      max-width: 220px;
      width: 100%;
      border: 1px solid var(--ds-color-border);
      border-radius: 999px;
      padding: 9px 12px;
      background: #fff;
    }

    .user-badge {
      display: inline-block;
      padding: 7px 11px;
      background: var(--ds-color-surface-soft);
      border: 1px solid #c7d2fe;
      border-radius: 999px;
      color: #3730a3;
      font-size: 13px;
      font-weight: 600;
    }

    .logout-form { display: inline; }

    .card {
      background: var(--ds-color-surface);
      border: 1px solid var(--ds-color-border);
      border-radius: var(--ds-radius-md);
      box-shadow: var(--ds-shadow-sm);
      padding: var(--ds-space-4);
      margin-bottom: var(--ds-space-4);
    }

    .card h2, .card h3 { margin: 0 0 10px; }
    .card p + p { margin-top: 8px; }

    .page-lead {
      background: linear-gradient(140deg, #0f172a 0%, #1d4ed8 100%);
      color: #dbeafe;
      border: none;
    }

    .eyebrow {
      text-transform: uppercase;
      letter-spacing: .08em;
      font-size: 12px;
      margin-bottom: 8px;
      color: #bfdbfe;
    }

    .muted { color: var(--ds-color-text-muted); }
    .stack-sm > * + * { margin-top: 8px; }
    .stack-md > * + * { margin-top: 12px; }
    .mt-sm { margin-top: 10px; }

    .row { display: flex; gap: var(--ds-space-3); flex-wrap: wrap; }
    .row > * { flex: 1; min-width: 180px; }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 44px;
      min-height: 42px;
      padding: 10px 14px;
      border: none;
      border-radius: var(--ds-radius-sm);
      background: var(--ds-color-primary);
      color: #fff;
      text-decoration: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
    }

    .btn:hover, .btn:focus-visible { background: var(--ds-color-primary-strong); outline: none; }
    .btn.secondary { background: #334155; }
    .btn.secondary:hover, .btn.secondary:focus-visible { background: #1f2937; }
    .btn.compact { min-height: 36px; padding: 6px 10px; }
    .primary-cta-button { min-height: 48px; border-radius: 12px; }

    input, select, textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: var(--ds-radius-sm);
      background: #fff;
      font: inherit;
    }

    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: #93c5fd;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.16);
    }

    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #eef2f7; text-align: left; }
    th { color: #334155; font-size: 13px; }

    .status-badge { display: inline-block; padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .status-active { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .status-mvp { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    .status-placeholder { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }

    .metric-value { font-size: 28px; font-weight: 700; color: #0b1326; }

    .dashboard-kpi-card {
      background: var(--ds-color-surface);
      border: 1px solid var(--ds-color-border);
      border-radius: 12px;
      padding: 14px;
    }

    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
    }

    .quick-actions-grid .btn { width: 100%; }

    .prospect-summary-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
      align-items: start;
    }

    .prospect-meta-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 8px 12px;
      margin-top: 10px;
    }

    .message-item {
      border-top: 1px solid #e2e8f0;
      padding-top: 10px;
      margin-top: 10px;
    }

    .message-meta { margin: 0; font-size: 13px; }
    .message-content { margin-top: 6px; }

    .pipeline-board {
      padding: 12px;
      overflow-x: auto;
    }

    .pipeline-columns {
      display: flex;
      gap: 12px;
      min-width: max-content;
      align-items: flex-start;
    }

    .pipeline-stage {
      width: 290px;
      max-width: 82vw;
      background: #f8fafc;
      border: 1px solid #dbe3ef;
      border-radius: 12px;
      padding: 10px;
    }

    .pipeline-stage-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }

    .pipeline-card {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px;
      margin-bottom: 8px;
    }

    .pipeline-card-link { text-decoration: none; color: #0f172a; display: block; }

    .pipeline-form-row { display: flex; gap: 6px; margin-top: 8px; align-items: center; }
    .pipeline-form-row select { font-size: 12px; padding: 7px; }

    .import-help { color: #6b7280; margin: 8px 0 14px; }
    .errors {
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 10px;
      padding: 10px 14px;
      color: #991b1b;
      margin-bottom: 14px;
    }

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

    .global-state.loading { border-color: #bfdbfe; background: #eff6ff; }
    .global-state.empty { border-color: #cbd5e1; background: #f8fafc; }
    .global-state.error { border-color: #fecaca; background: #fef2f2; }

    .state-dot { width: 12px; height: 12px; border-radius: 999px; flex-shrink: 0; background: currentColor; }
    .global-state.loading .state-dot { color: var(--ds-color-primary); }
    .global-state.empty .state-dot { color: #64748b; }
    .global-state.error .state-dot { color: var(--ds-color-danger); }

    .loading-skeleton-card {
      position: relative;
      overflow: hidden;
      border-radius: var(--ds-radius-md);
      border: 1px solid var(--ds-color-border);
      background: #fff;
      padding: var(--ds-space-4);
      margin-bottom: var(--ds-space-3);
    }

    .skeleton-line {
      height: 12px;
      border-radius: 999px;
      background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
      background-size: 220% 100%;
      animation: shimmer 1.6s infinite;
      margin-bottom: var(--ds-space-2);
    }

    @keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

    .empty-state-guided { text-align: center; padding: var(--ds-space-5); }
    .empty-state-guided h3 { margin: 0 0 var(--ds-space-2); }

    .bottom-nav {
      position: fixed; left: 0; right: 0; bottom: 0; height: var(--bottom-nav-height); background: #fff;
      border-top: 1px solid var(--ds-color-border); box-shadow: 0 -8px 20px rgba(15, 23, 42, 0.08);
      display: grid; grid-template-columns: repeat(5, 1fr); z-index: 40;
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
      padding: 6px;
    }

    .bottom-nav-link.active { color: var(--ds-color-primary-strong); font-weight: 700; }
    .bottom-nav-link span[aria-hidden='true'] { font-size: 18px; }

    @media (min-width: 900px) {
      .app-shell { grid-template-columns: 280px 1fr; }
      .sidebar { display: block; }
      .workspace { padding: var(--ds-space-5); }
      .bottom-nav { display: none; }
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
  <div class="container login-container">
<?php else: ?>
  <div class="app-shell">
    <?php if ($isAuthenticated): ?>
      <?php require __DIR__ . '/../components/navigation/desktop_sidebar.php'; ?>
    <?php endif; ?>

    <main class="workspace">
      <div class="container">
        <?php require __DIR__ . '/../components/navigation/topbar_compact.php'; ?>
<?php endif; ?>
