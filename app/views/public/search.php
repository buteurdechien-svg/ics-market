<div class="wrap">
  <div class="section-title"><h2><?= $q!=='' ? 'Résultats pour « '.e($q).' »' : 'Tous les produits' ?></h2>
    <span class="muted"><?= count($rows) ?> produit(s)</span></div>

  <?php if ($dbError): ?>
    <div class="alert alert-error">Erreur lors de la recherche : <?= e($dbError) ?></div>
  <?php endif; ?>

  <?php if (!$rows && !$dbError): ?>
    <p class="muted">Aucun produit ne correspond à votre recherche.</p>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($rows as $p): ?>
      <a class="card prod" href="/p/<?= (int)$p['id'] ?>">
        <div class="thumb"><img src="/assets/img/<?= e($p['image'] ?? 'logo.svg') ?>" alt="<?= e($p['name']) ?>"></div>
        <div class="body">
          <div class="name"><?= e($p['name']) ?></div>
          <div class="stars"><?= stars((float)($p['rating_avg'] ?? 0)) ?></div>
          <div class="price"><?= e(price((int)$p['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
