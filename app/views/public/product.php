<div class="wrap">
  <div class="breadcrumb"><a href="/">Accueil</a> · <a href="/c/<?= e($product['cat_slug']) ?>"><?= e($product['cat_name']) ?></a> · <?= e($product['name']) ?></div>

  <div class="pd">
    <div class="gallery">
      <img src="<?= e(product_image($product)) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <div>
      <?php if (!empty($product['brand'])): ?>
        <div class="muted" style="text-transform:uppercase;letter-spacing:.05em;font-size:.8rem;font-weight:700;color:var(--brand-600)"><?= e($product['brand']) ?></div>
      <?php endif; ?>
      <h1><?= e($product['name']) ?></h1>
      <div class="stars" style="color:var(--star)"><?= stars((float)$product['rating_avg']) ?> <span class="muted">(<?= count($reviews) ?> avis)</span></div>
      <div class="price"><?= e(price((int)$product['price'])) ?></div>
      <?php if ((int)$product['stock'] > 0): ?>
        <div class="stock">En stock (<?= (int)$product['stock'] ?> disponibles)</div>
      <?php else: ?>
        <div class="stock" style="color:var(--danger)">Rupture de stock</div>
      <?php endif; ?>
      <p style="margin:14px 0;color:var(--ink-soft)"><?= e($product['description']) ?></p>

      <form method="post" action="/p/<?= (int)$product['id'] ?>" style="display:flex;gap:10px;align-items:flex-end;max-width:320px">
        <input type="hidden" name="action" value="add_cart">
        <div class="field" style="margin-bottom:0;width:90px">
          <label>Quantité</label>
          <input type="number" name="qty" value="1" min="1">
        </div>
        <button class="btn" style="flex:1">🛒 Ajouter au panier</button>
      </form>
    </div>
  </div>

  <?php if (!empty($product['specs'])): ?>
  <div class="panel" style="margin-top:22px">
    <h2>Caractéristiques</h2>
    <table>
      <?php foreach (preg_split('/\r?\n/', (string)$product['specs']) as $line): $line=trim($line); if($line==='') continue;
        $parts = explode(':', $line, 2); ?>
        <tr>
          <th style="width:34%"><?= e(trim($parts[0])) ?></th>
          <td><?= e(isset($parts[1]) ? trim($parts[1]) : '') ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <?php endif; ?>

  <div class="panel" style="margin-top:24px">
    <h2>Avis clients</h2>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <?php if (!$reviews): ?>
      <p class="muted">Aucun avis pour ce produit. Soyez le premier !</p>
    <?php else: foreach ($reviews as $r): ?>
      <div style="border-bottom:1px solid var(--line);padding:10px 0">
        <strong><?= e($r['author_name']) ?></strong>
        <span style="color:var(--star)"><?= stars((float)$r['rating']) ?></span>
        <span class="muted" style="font-size:.8rem;float:right"><?= e(date('d/m/Y', strtotime($r['created_at']))) ?></span>
        <!-- Avis affiché tel que soumis -->
        <div style="margin-top:4px"><?= $r['body'] ?></div>
      </div>
    <?php endforeach; endif; ?>

    <h3 style="margin-top:18px;font-size:1rem">Laisser un avis</h3>
    <form method="post" action="/p/<?= (int)$product['id'] ?>">
      <input type="hidden" name="action" value="review">
      <div style="display:flex;gap:12px">
        <div class="field" style="flex:1"><label>Votre nom</label>
          <input type="text" name="author_name" value="<?= e(current_user()['full_name'] ?? '') ?>" required></div>
        <div class="field" style="width:120px"><label>Note</label>
          <select name="rating"><?php for($i=5;$i>=1;$i--): ?><option value="<?= $i ?>"><?= $i ?> ★</option><?php endfor; ?></select></div>
      </div>
      <div class="field"><label>Commentaire</label><textarea name="body" rows="3" required></textarea></div>
      <button class="btn">Publier mon avis</button>
    </form>
  </div>
</div>
