<div class="card" style="background:linear-gradient(135deg,#1e1b4b,#312e81);color:#eef2ff;border:none;">
  <h2 style="margin-top:0;">Vision produit complète</h2>
  <p style="margin:0;max-width:780px;opacity:0.92;">Toute l'architecture est visible dans l'admin: 14 modules, statuts unifiés, hiérarchie claire et parcours stable même lorsque certains blocs ne sont pas encore finalisés.</p>
</div>

<div class="row">
  <div class="card">
    <p class="muted" style="margin-top:0;">Modules actifs</p>
    <h3 style="margin:0;"><?= (int) ($statusCounts['Actif'] ?? 0) ?></h3>
  </div>
  <div class="card">
    <p class="muted" style="margin-top:0;">Modules bêta</p>
    <h3 style="margin:0;"><?= (int) ($statusCounts['Bêta'] ?? 0) ?></h3>
  </div>
  <div class="card">
    <p class="muted" style="margin-top:0;">En cours de développement</p>
    <h3 style="margin:0;"><?= (int) ($statusCounts['En cours de développement'] ?? 0) ?></h3>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Cœur produit prioritaire</h3>
  <div class="row">
    <?php foreach (($coreModules ?? []) as $module): ?>
      <div style="border:1px solid #e2e8f0;border-radius:12px;padding:12px;background:#f8fafc;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;">
          <strong><?= htmlspecialchars((string) $module['icon']) ?> <?= htmlspecialchars((string) $module['label']) ?></strong>
          <span class="badge <?= $module['status'] === 'Actif' ? 'actif' : ($module['status'] === 'Bêta' ? 'beta' : 'dev') ?>"><?= htmlspecialchars((string) $module['status']) ?></span>
        </div>
        <p class="muted" style="margin-bottom:0;"><?= htmlspecialchars((string) $module['description']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <h3 style="margin-top:0;">Architecture modules</h3>
  <table>
    <thead>
      <tr>
        <th>Module</th>
        <th>Statut</th>
        <th>Route admin</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($modules ?? []) as $module): ?>
        <tr>
          <td><a href="<?= htmlspecialchars((string) $module['route']) ?>" style="color:#1d4ed8;text-decoration:none;"><?= htmlspecialchars((string) $module['label']) ?></a></td>
          <td><span class="badge <?= $module['status'] === 'Actif' ? 'actif' : ($module['status'] === 'Bêta' ? 'beta' : 'dev') ?>"><?= htmlspecialchars((string) $module['status']) ?></span></td>
          <td><code><?= htmlspecialchars((string) $module['route']) ?></code></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
