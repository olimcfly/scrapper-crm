<?php
$sheetFilters = is_array($sheetFilters ?? null) ? $sheetFilters : [];
$sheetStatuses = is_array($sheetStatuses ?? null) ? $sheetStatuses : [];
$sheetSources = is_array($sheetSources ?? null) ? $sheetSources : [];
$sheetCategoryOrder = is_array($sheetCategoryOrder ?? null) ? $sheetCategoryOrder : [];
$sheetActiveCategory = (string) ($sheetActiveCategory ?? 'Tous');

$awarenessValue = (string) ($sheetFilters['awareness_level'] ?? '');
$socialValue = (string) ($sheetFilters['social_presence'] ?? '');
$websiteValue = (string) ($sheetFilters['website_presence'] ?? '');
$priorityValue = (string) ($sheetFilters['priority'] ?? '');
$zoneValue = (string) ($sheetFilters['zone_scope'] ?? '');
?>

<div class="bottom-sheet-backdrop" data-sheet-backdrop aria-hidden="true"></div>
<form method="get" action="/prospects" class="bottom-sheet" data-filter-sheet aria-hidden="true" aria-label="Filtres avancés prospects">
  <div class="sheet-handle" aria-hidden="true"></div>

  <div class="sheet-header-row">
    <div>
      <h3 style="margin:0 0 4px;">Filtres avancés</h3>
      <p class="muted" style="margin:0;">Affinez vite les prospects prioritaires.</p>
    </div>
    <button type="button" class="sheet-close-btn" data-close-sheet aria-label="Fermer les filtres">✕</button>
  </div>

  <input type="hidden" name="q" value="<?= htmlspecialchars((string) ($sheetFilters['q'] ?? '')) ?>">
  <input type="hidden" name="category" value="<?= htmlspecialchars($sheetActiveCategory) ?>" data-sheet-category>

  <div class="sheet-grid">
    <label>
      Ville
      <input type="text" name="city" placeholder="Ex: Lyon" value="<?= htmlspecialchars((string) ($sheetFilters['city'] ?? '')) ?>">
    </label>

    <label>
      Catégorie
      <select name="category_select" data-sheet-category-select>
        <?php foreach ($sheetCategoryOrder as $category): ?>
          <option value="<?= htmlspecialchars((string) $category) ?>" <?= ((string) $category === $sheetActiveCategory) ? 'selected' : '' ?>><?= htmlspecialchars((string) $category) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Niveau de conscience
      <select name="awareness_level">
        <option value="">Tous</option>
        <option value="À éduquer" <?= $awarenessValue === 'À éduquer' ? 'selected' : '' ?>>À éduquer</option>
        <option value="Conscient du besoin" <?= $awarenessValue === 'Conscient du besoin' ? 'selected' : '' ?>>Conscient du besoin</option>
        <option value="Prêt à décider" <?= $awarenessValue === 'Prêt à décider' ? 'selected' : '' ?>>Prêt à décider</option>
      </select>
    </label>

    <label>
      Présence site web
      <select name="website_presence">
        <option value="">Tous</option>
        <option value="oui" <?= $websiteValue === 'oui' ? 'selected' : '' ?>>Oui</option>
        <option value="non" <?= $websiteValue === 'non' ? 'selected' : '' ?>>Non</option>
      </select>
    </label>

    <label>
      Présence réseaux sociaux
      <select name="social_presence">
        <option value="">Tous</option>
        <option value="oui" <?= $socialValue === 'oui' ? 'selected' : '' ?>>Oui</option>
        <option value="non" <?= $socialValue === 'non' ? 'selected' : '' ?>>Non</option>
      </select>
    </label>

    <label>
      Priorité
      <select name="priority">
        <option value="">Toutes</option>
        <option value="eleve" <?= $priorityValue === 'eleve' ? 'selected' : '' ?>>Haute</option>
        <option value="moyen" <?= $priorityValue === 'moyen' ? 'selected' : '' ?>>Moyenne</option>
        <option value="faible" <?= $priorityValue === 'faible' ? 'selected' : '' ?>>Basse</option>
      </select>
    </label>

    <label>
      Statut CRM
      <select name="status_id">
        <option value="0">Tous</option>
        <?php foreach ($sheetStatuses as $status): ?>
          <option value="<?= (int) ($status['id'] ?? 0) ?>" <?= ((int) ($sheetFilters['status_id'] ?? 0) === (int) ($status['id'] ?? 0)) ? 'selected' : '' ?>>
            <?= htmlspecialchars((string) ($status['name'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Zone d’activité
      <select name="zone_scope">
        <option value="">Toutes</option>
        <option value="Locale" <?= $zoneValue === 'Locale' ? 'selected' : '' ?>>Locale</option>
        <option value="Multi-zone" <?= $zoneValue === 'Multi-zone' ? 'selected' : '' ?>>Multi-zone</option>
      </select>
    </label>

    <label class="full">
      Source
      <select name="source_id">
        <option value="0">Toutes les sources</option>
        <?php foreach ($sheetSources as $source): ?>
          <option value="<?= (int) ($source['id'] ?? 0) ?>" <?= ((int) ($sheetFilters['source_id'] ?? 0) === (int) ($source['id'] ?? 0)) ? 'selected' : '' ?>>
            <?= htmlspecialchars((string) ($source['name'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
  </div>

  <div class="sheet-actions">
    <a class="btn secondary" href="/prospects" style="flex:1;">Réinitialiser</a>
    <button class="finder-btn secondary" type="submit" style="flex:1;">Appliquer les filtres</button>
  </div>
</form>
