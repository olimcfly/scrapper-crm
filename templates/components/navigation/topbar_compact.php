<?php
$rawPath = is_string($currentPath ?? null) ? trim((string) $currentPath) : '';
$cleanPath = trim($rawPath, '/');
$segments = $cleanPath === '' ? [] : array_values(array_filter(explode('/', $cleanPath), static fn (string $segment): bool => $segment !== ''));

$sectionByRoot = [
  'dashboard' => 'Tableau de bord',
  'prospects' => 'Prospects',
  'pipeline' => 'Pipeline commercial',
  'messages-ia' => 'Messages',
  'messages' => 'Messages',
  'strategie' => 'Stratégie',
  'contenu' => 'Contenu',
  'settings' => 'Paramètres',
  'admin' => 'Administration',
];

$section = 'CRM';
if ($segments !== []) {
  $section = $sectionByRoot[$segments[0]] ?? ucfirst(str_replace('-', ' ', $segments[0]));
}

$breadcrumbItems = [$section];
if (count($segments) > 1) {
  foreach (array_slice($segments, 1) as $segment) {
    if (ctype_digit($segment)) {
      continue;
    }
    $breadcrumbItems[] = ucfirst(str_replace('-', ' ', $segment));
  }
}

if ($breadcrumbItems === [$section] && !empty($title ?? '')) {
  $normalizedTitle = trim((string) $title);
  if ($normalizedTitle !== '' && strcasecmp($normalizedTitle, $section) !== 0) {
    $breadcrumbItems[] = $normalizedTitle;
  }
}

$contextLabel = count($segments) > 1 ? 'Section active' : 'Espace CRM';
?>
<header class="topbar" role="banner">
  <div class="topbar-inner">
    <div class="topbar-left">
      <p class="topbar-context"><?= htmlspecialchars($contextLabel) ?></p>
      <p class="topbar-breadcrumb" aria-label="Fil d’Ariane"><?= htmlspecialchars(implode(' / ', $breadcrumbItems)) ?></p>
    </div>

    <div class="topbar-right topbar-meta">
      <?php if (!empty($authUser['first_name'] ?? '')): ?>
        <span class="topbar-user">
          Bonjour, <?= htmlspecialchars((string) $authUser['first_name']) ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</header>
