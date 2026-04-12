<section class="card empty-state-guided">
  <p style="font-size:28px;margin:0 0 8px;" aria-hidden="true">🧭</p>
  <h3><?= htmlspecialchars((string) ($emptyTitle ?? 'Aucune donnée pour le moment')) ?></h3>
  <p class="muted"><?= htmlspecialchars((string) ($emptyDescription ?? 'Ajoutez un premier prospect pour démarrer le flux prospect-first.')) ?></p>
  <?php if (!empty($emptyCtaHref) && !empty($emptyCtaLabel)): ?>
    <?php
      $ctaLabel = (string) $emptyCtaLabel;
      $ctaHref = (string) $emptyCtaHref;
      require __DIR__ . '/../ui/primary_cta_button.php';
    ?>
  <?php endif; ?>
</section>
