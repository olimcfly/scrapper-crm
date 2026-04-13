<section class="card empty-guided" aria-live="polite">
  
  <p class="empty-eyebrow">Empty state</p>

  <h3 class="empty-title">
    <?= htmlspecialchars($emptyTitle) ?>
  </h3>

  <p class="empty-description">
    <?= htmlspecialchars($emptyDescription) ?>
  </p>

  <div class="empty-cta">
    <?php
      $label = $emptyCtaLabel;
      $href = $emptyCtaHref;
      $icon = '➕';
      include __DIR__ . '/PrimaryCTAButton.php';
    ?>
  </div>

</section>