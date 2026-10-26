<div class="wrap">
  <div class="breadcrumb"><a href="/">Accueil</a> · <?= e($category['name']) ?></div>
  <div class="section-title"><h2><?= e($category['name']) ?></h2><span class="muted"><?= count($products) ?> produit(s)</span></div>
  <?php if (!$products): ?>
    <p class="muted">Aucun produit dans cette catégorie.</p>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($products as $p): ?>
      <a class="card prod" href="/p/<?= (int)$p['id'] ?>">
        <div class="thumb"><img src="<?= e(product_image($p)) ?>" alt="<?= e($p['name']) ?>"></div>
        <div class="body">
          <div class="muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em"><?= e($p['brand'] ?? '') ?></div>
          <div class="name"><?= e($p['name']) ?></div>
          <div class="stars"><?= stars((float)$p['rating_avg']) ?></div>
          <div class="price"><?= e(price((int)$p['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
