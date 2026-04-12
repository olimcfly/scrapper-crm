<?php
$badgeLabel = (string) ($badgeLabel ?? '');
$badgeClass = (string) ($badgeClass ?? '');
$badgeTitle = (string) ($badgeTitle ?? $badgeLabel);

if ($badgeLabel === '') {
    return;
}
?>
<span class="<?= htmlspecialchars(trim('prospect-badge ' . $badgeClass)) ?>" title="<?= htmlspecialchars($badgeTitle) ?>">
  <?= htmlspecialchars($badgeLabel) ?>
</span>
