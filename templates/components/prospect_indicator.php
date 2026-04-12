<?php
$indicatorLabel = (string) ($indicatorLabel ?? '');
$indicatorValue = (string) ($indicatorValue ?? '');
$indicatorState = (string) ($indicatorState ?? 'neutral');

if ($indicatorLabel === '' || $indicatorValue === '') {
    return;
}
?>
<div class="presence-item">
  <span><?= htmlspecialchars($indicatorLabel) ?></span>
  <span class="presence-<?= htmlspecialchars($indicatorState) ?>"><?= htmlspecialchars($indicatorValue) ?></span>
</div>
