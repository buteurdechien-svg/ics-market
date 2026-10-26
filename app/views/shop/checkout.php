<div class="wrap">
  <h1>Commander</h1>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
    <div class="panel">
      <h2>Livraison &amp; paiement</h2>
      <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
      <form method="post" action="/checkout">
        <?= csrf_field() ?>
        <div class="field"><label>Adresse de livraison</label>
          <input type="text" name="address" placeholder="Cocody, Abidjan" required></div>

        <label style="font-weight:600;display:block;margin:6px 0 8px">Moyen de paiement</label>
        <div class="paymethods">
          <?php $first=true; foreach ($methods as $key=>$m): ?>
            <label class="paymethod">
              <input type="radio" name="method" value="<?= e($key) ?>" <?= $first?'required':'' ?>>
              <span class="pm-badge pm-<?= e($key) ?>"><?= e($m['label']) ?></span>
              <span class="pm-tag"><?= e($m['tag']) ?></span>
            </label>
          <?php $first=false; endforeach; ?>
        </div>

        <?php foreach ($items as $it): $p=$it['p']; ?>
          <input type="hidden" name="qty[<?= (int)$p['id'] ?>]" value="<?= (int)$it['qty'] ?>">
          <input type="hidden" name="unit_price[<?= (int)$p['id'] ?>]" value="<?= (int)$p['price'] ?>">
        <?php endforeach; ?>

        <button class="btn btn-block" style="margin-top:14px">Continuer vers le paiement</button>
      </form>
    </div>
    <div>
      <div class="panel summary">
        <h2>Votre commande</h2>
        <?php foreach ($items as $it): ?>
          <div class="line"><span><?= e($it['p']['name']) ?> × <?= (int)$it['qty'] ?></span><span><?= e(price((int)$it['line'])) ?></span></div>
        <?php endforeach; ?>
        <?php if ($coupon): ?><div class="line muted"><span>Code promo</span><span><?= e($coupon) ?></span></div><?php endif; ?>
        <div class="line total"><span>Total</span><span><?= e(price((int)$subtotal)) ?></span></div>
      </div>
    </div>
  </div>
</div>
