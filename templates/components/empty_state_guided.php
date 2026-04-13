<section class="card empty-state-guided" aria-label="État vide">
  
  <p class="empty-eyebrow">Prêt à démarrer</p>

  <h3 class="empty-title">
    <?= htmlspecialchars($title) ?>
  </h3>

  <p class="empty-message">
    <?= htmlspecialchars($message) ?>
  </p>

  <div class="empty-cta">
    <?php
      $href = $ctaHref;
      $label = $ctaLabel;
      $variant = 'primary';
      require __DIR__ . '/primary_cta_button.php';
    ?>
  </div>

</section>