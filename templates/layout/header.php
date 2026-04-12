<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'CRM') ?></title>
  <style>
    :root {
      --bg: #f2f5fb;
      --panel: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
      --line: #e2e8f0;
      --primary: #4f46e5;
      --primary-soft: #e0e7ff;
      --ok: #15803d;
      --ok-soft: #dcfce7;
      --beta: #1d4ed8;
      --beta-soft: #dbeafe;
      --dev: #b45309;
      --dev-soft: #fef3c7;
      --danger: #dc2626;
      --sidebar-bg: #0b1224;
      --sidebar-text: #cbd5e1;
      --sidebar-active-bg: #1e293b;
      --sidebar-active-text: #f8fafc;
      --font-sans: "Inter", "Segoe UI", Arial, sans-serif;
    }
    * { box-sizing: border-box; }
    body { margin: 0; background: var(--bg); color: var(--text); font-family: var(--font-sans); }
    .app-shell { display: grid; grid-template-columns: 300px minmax(0, 1fr); min-height: 100vh; }
    .sidebar { background: linear-gradient(180deg, #0f172a 0%, var(--sidebar-bg) 100%); color: var(--sidebar-text); padding: 24px 18px; }
    .brand { font-size: 19px; font-weight: 700; color: #fff; margin-bottom: 16px; }
    .brand-sub { font-size: 12px; color: #94a3b8; margin-bottom: 20px; }
    .user-chip { display: inline-block; background: rgba(79, 70, 229, 0.2); border: 1px solid rgba(129, 140, 248, 0.45); color: #e2e8f0; border-radius: 999px; padding: 6px 10px; font-size: 12px; margin-bottom: 18px; }
    .sidebar nav { display: grid; gap: 8px; }
    .nav-link { display: flex; align-items: center; justify-content: space-between; gap: 10px; color: var(--sidebar-text); text-decoration: none; padding: 9px 10px; border-radius: 10px; border: 1px solid transparent; font-size: 14px; }
    .nav-link.active { background: var(--sidebar-active-bg); border-color: #334155; color: var(--sidebar-active-text); }
    .nav-meta { display: inline-flex; align-items: center; gap: 8px; min-width: 0; }
    .badge { display: inline-block; border-radius: 999px; padding: 3px 9px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge.actif { color: var(--ok); background: var(--ok-soft); }
    .badge.beta { color: var(--beta); background: var(--beta-soft); }
    .badge.dev { color: var(--dev); background: var(--dev-soft); }
    .main { padding: 22px; }
    .topbar { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 16px; }
    .topbar-title { font-size: 24px; margin: 0; }
    .container { max-width: 1180px; }
    .card { background: var(--panel); border: 1px solid var(--line); border-radius: 14px; padding: 18px; margin-bottom: 16px; box-shadow: 0 6px 22px rgba(15, 23, 42, 0.05); }
    .row { display: flex; gap: 12px; flex-wrap: wrap; }
    .row > * { flex: 1; min-width: 220px; }
    input, select, textarea { width: 100%; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #eef2f7; text-align: left; }
    .btn { display: inline-block; padding: 8px 12px; background: var(--primary); color: #fff; border: none; border-radius: 8px; text-decoration: none; cursor: pointer; }
    .btn.secondary { background: #475569; }
    .btn.danger { background: var(--danger); }
    .errors { background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
    .muted { color: var(--muted); }
    @media (max-width: 1000px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; }
      .main { padding-top: 8px; }
    }
  </style>
</head>
<body>
<?php $isLoginPage = ($title ?? '') === 'Connexion'; ?>
<?php if (!$isLoginPage): ?>
<?php
  $isAuthenticated = is_array($authUser ?? null) && isset($authUser['id']);
  $path = (string) ($currentPath ?? '/');
  $badgeClassMap = ['Actif' => 'actif', 'Bêta' => 'beta', 'En cours de développement' => 'dev'];
?>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">SCRAPPER CRM</div>
    <div class="brand-sub">Admin premium · architecture produit</div>
    <?php if ($isAuthenticated): ?>
      <div class="user-chip"><?= htmlspecialchars((string) ($authUser['name'] ?? 'Utilisateur')) ?> · <?= htmlspecialchars((string) ($authUser['email'] ?? '')) ?></div>
      <nav>
        <a href="/admin" class="nav-link<?= $path === '/admin' ? ' active' : '' ?>">
          <span class="nav-meta">🏠 Dashboard</span>
        </a>
        <?php foreach (($adminModules ?? []) as $module): ?>
          <?php
            $status = (string) $module['status'];
            $badgeClass = $badgeClassMap[$status] ?? 'dev';
            $isActive = str_starts_with($path, (string) $module['route']) || ($path === '/prospects/create' && $module['slug'] === 'contacts');
          ?>
          <a href="<?= htmlspecialchars((string) $module['route']) ?>" class="nav-link<?= $isActive ? ' active' : '' ?>">
            <span class="nav-meta"><?= htmlspecialchars((string) $module['icon']) ?> <?= htmlspecialchars((string) $module['label']) ?></span>
            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <form method="post" action="/logout" style="margin-top:16px;">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? "")) ?>">
        <button type="submit" class="btn secondary" style="width:100%;">Déconnexion</button>
      </form>
    <?php else: ?>
      <a href="/login" class="btn">Connexion</a>
    <?php endif; ?>
  </aside>
  <main class="main">
    <div class="container">
      <div class="topbar">
        <h1 class="topbar-title"><?= htmlspecialchars((string) ($title ?? 'CRM')) ?></h1>
      </div>
<?php endif; ?>
