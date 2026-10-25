<div class="wrap">
  <div class="breadcrumb"><a href="/orders">Mes commandes</a> · <?= e($order['reference']) ?></div>
  <h1>Commande <?= e($order['reference']) ?></h1>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
    <div class="panel">
      <h2>Articles</h2>
      <table>
        <thead><tr><th>Produit</th><th>Prix unit.</th><th>Qté</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($items as $i): ?>
          <tr><td><?= e($i['name']) ?></td><td><?= e(price((int)$i['unit_price'])) ?></td>
              <td><?= (int)$i['qty'] ?></td><td><?= e(price((int)$i['unit_price'] * (int)$i['qty'])) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="panel">
      <h2>Récapitulatif</h2>
      <p class="muted">Client : <?= e($order['full_name'] ?? '—') ?><br>
         Adresse : <?= e($order['address'] ?? '—') ?><br>
         Statut : <span class="badge badge-role"><?= e($order['status']) ?></span></p>
      <div class="summary">
        <div class="line"><span>Sous-total</span><span><?= e(price((int)$order['subtotal'])) ?></span></div>
        <div class="line"><span>Remise</span><span>-<?= e(price((int)$order['discount'])) ?></span></div>
        <div class="line total"><span>Total</span><span><?= e(price((int)$order['total'])) ?></span></div>
      </div>
      <?php if (!empty($order['invoice'])): ?>
        <a href="/invoice?file=<?= urlencode($order['invoice']) ?>" class="btn btn-ghost btn-block" style="margin-top:12px">Télécharger la facture</a>
      <?php endif; ?>
    </div>
  </div>
</div>
