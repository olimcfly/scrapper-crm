<section class="card empty-state-guided">

  <div class="empty-icon" aria-hidden="true">🧭</div>

  <h3 class="empty-title">
    <?= htmlspecialchars((string) ($emptyTitle ?? 'Aucune donnée pour le moment')) ?>
  </h3>

  <p class="empty-message">
    <?= htmlspecialchars((string) ($emptyDescription ?? 'Ajoutez un premier prospect pour démarrer le flux prospect-first.')) ?>
  </p>

  <?php if (!empty($emptyCtaHref) && !empty($emptyCtaLabel)): ?>
    <div class="empty-cta">
      <?php
        $ctaLabel = (string) $emptyCtaLabel;
        $ctaHref = (string) $emptyCtaHref;
        require __DIR__ . '/../ui/primary_cta_button.php';
      ?>
    </div>
  <?php endif; ?>

</section>