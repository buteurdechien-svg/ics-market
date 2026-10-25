<div class="wrap">
  <h1>Commander</h1>
  <?php if ($receipt): ?>
    <div class="panel">
      <div class="alert alert-ok">Commande confirmée et payée (paiement simulé).</div>
      <table>
        <tr><th>Référence</th><td><?= e($receipt['ref']) ?></td></tr>
        <tr><th>Total payé</th><td><?= e(price((int)$receipt['total'])) ?></td></tr>
      </table>
      <p style="margin-top:14px">
        <a href="/order/<?= (int)$receipt['oid'] ?>" class="btn">Voir ma commande</a>
        <a href="/invoice?file=<?= urlencode($receipt['invoice']) ?>" class="btn btn-ghost">Télécharger la facture</a>
        <a href="/" class="btn btn-ghost">Continuer mes achats</a>
      </p>
    </div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
    <div class="panel">
      <h2>Livraison &amp; paiement</h2>
      <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
      <form method="post" action="/checkout">
        <?= csrf_field() ?>
        <div class="field"><label>Adresse de livraison</label>
          <input type="text" name="address" placeholder="Cocody, Abidjan" required></div>
        <?php foreach ($items as $it): $p=$it['p']; ?>
          <input type="hidden" name="qty[<?= (int)$p['id'] ?>]" value="<?= (int)$it['qty'] ?>">
          <input type="hidden" name="unit_price[<?= (int)$p['id'] ?>]" value="<?= (int)$p['price'] ?>">
        <?php endforeach; ?>
        <p class="muted">Paiement simulé — aucune transaction réelle.</p>
        <button class="btn btn-block">Payer <?= e(price((int)$subtotal)) ?></button>
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
  <?php endif; ?>
</div>
