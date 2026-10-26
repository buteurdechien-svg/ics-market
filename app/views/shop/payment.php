<div class="wrap">
  <h1>Paiement</h1>

  <div class="alert alert-info" style="margin-bottom:16px">
    ⚠️ <strong>Site fictif de formation.</strong> N'entrez <u>jamais</u> un vrai numéro de carte,
    un vrai code PIN ou un vrai cryptogramme. Utilisez des valeurs de test
    (ex. carte <code>4111 1111 1111 1111</code>, exp. <code>12/30</code>, CVV <code>123</code>).
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr;gap:22px">
    <div class="panel">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <span class="pm-badge pm-<?= e($method) ?>"><?= e($label) ?></span>
        <span class="muted"><?= $isCard ? 'Carte bancaire' : 'Mobile money' ?></span>
      </div>

      <?php foreach ($errors as $er): ?><div class="alert alert-error"><?= e($er) ?></div><?php endforeach; ?>

      <form method="post" action="/payment">
        <input type="hidden" name="amount" value="<?= (int)$pending['total'] ?>">
        <input type="hidden" name="status" value="paid">

        <?php if ($isCard): ?>
          <div class="field"><label>Titulaire de la carte</label>
            <input type="text" name="pay_name" placeholder="NOM Prénom" value="<?= e($user['full_name'] ?? '') ?>"></div>
          <div class="field"><label>Numéro de carte</label>
            <input type="text" name="pay_number" inputmode="numeric" autocomplete="off" placeholder="4111 1111 1111 1111"></div>
          <div style="display:flex;gap:12px">
            <div class="field" style="flex:1"><label>Expiration (MM/AA)</label>
              <input type="text" name="card_expiry" autocomplete="off" placeholder="12/30"></div>
            <div class="field" style="width:120px"><label>Cryptogramme (CVV)</label>
              <input type="text" name="card_cvv" inputmode="numeric" autocomplete="off" placeholder="123" maxlength="4"></div>
          </div>
        <?php else: ?>
          <div class="field"><label>Nom du titulaire (facultatif)</label>
            <input type="text" name="pay_name" placeholder="NOM Prénom" value="<?= e($user['full_name'] ?? '') ?>"></div>
          <div class="field"><label>Numéro <?= e($label) ?></label>
            <input type="text" name="pay_number" inputmode="tel" autocomplete="off" placeholder="07 00 00 00 00"></div>
          <p class="muted" style="font-size:.85rem">Un code de confirmation vous serait normalement envoyé par SMS (simulé ici).</p>
        <?php endif; ?>

        <button class="btn btn-block" style="margin-top:8px">Payer <?= e(price((int)$pending['total'])) ?></button>
        <a href="/checkout" class="btn btn-ghost btn-block" style="margin-top:8px">Changer de moyen de paiement</a>
      </form>
    </div>

    <div>
      <div class="panel summary">
        <h2>Montant à régler</h2>
        <div class="line"><span>Sous-total</span><span><?= e(price((int)$pending['subtotal'])) ?></span></div>
        <?php if ((int)$pending['discount'] > 0): ?>
          <div class="line muted"><span>Remise</span><span>-<?= e(price((int)$pending['discount'])) ?></span></div>
        <?php endif; ?>
        <div class="line total"><span>Total</span><span><?= e(price((int)$pending['total'])) ?></span></div>
        <p class="muted" style="font-size:.82rem;margin-top:10px">Livraison : <?= e($pending['address']) ?></p>
      </div>
    </div>
  </div>
</div>
