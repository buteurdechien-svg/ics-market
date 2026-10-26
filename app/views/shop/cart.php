<div class="wrap">
  <h1>Mon panier</h1>
  <?php if (!$items): ?>
    <div class="panel"><p class="muted">Votre panier est vide.</p><a href="/" class="btn">Continuer mes achats</a></div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
    <div class="panel">
      <form method="post" action="/cart">
        <input type="hidden" name="action" value="update">
        <?php foreach ($items as $it): $p=$it['p']; ?>
          <div class="cart-row">
            <img src="<?= e(product_image($p)) ?>" alt="">
            <div style="flex:1">
              <a href="/p/<?= (int)$p['id'] ?>"><strong><?= e($p['name']) ?></strong></a>
              <div class="muted"><?= e(price((int)$p['price'])) ?> l'unité</div>
            </div>
            <input class="qty" type="number" name="qty[<?= (int)$p['id'] ?>]" value="<?= (int)$it['qty'] ?>">
            <div style="width:120px;text-align:right;font-weight:700"><?= e(price((int)$it['line'])) ?></div>
          </div>
        <?php endforeach; ?>
        <button class="btn btn-ghost" style="margin-top:12px">Mettre à jour</button>
      </form>
    </div>

    <div>
      <div class="panel summary">
        <h2>Récapitulatif</h2>
        <div class="line"><span>Sous-total</span><span><?= e(price((int)$subtotal)) ?></span></div>
        <form method="post" action="/cart" style="margin:10px 0">
          <input type="hidden" name="action" value="coupon">
          <label>Code promo</label>
          <div style="display:flex;gap:8px">
            <input type="text" name="coupon" value="<?= e($coupon) ?>" placeholder="BIENVENUE10">
            <button class="btn btn-dark" style="padding:11px 14px">OK</button>
          </div>
        </form>
        <div class="line total"><span>Total</span><span><?= e(price((int)$subtotal)) ?></span></div>
        <a href="/checkout" class="btn btn-block" style="margin-top:12px">Passer la commande</a>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
