<div class="wrap">
  <h1>Espace <?= e($user['role']) ?></h1>
  <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
  <?php if ($notice): ?><div class="alert alert-ok"><?= e($notice) ?></div><?php endif; ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">
    <div class="panel">
      <h2>Ajouter un produit</h2>
      <form method="post" action="/manage">
        <input type="hidden" name="action" value="add_product">
        <div class="field"><label>Nom</label><input type="text" name="name" required></div>
        <div style="display:flex;gap:12px">
          <div class="field" style="flex:1"><label>Prix (FCFA)</label><input type="number" name="price" min="1" required></div>
          <div class="field" style="flex:1"><label>Stock</label><input type="number" name="stock" min="0" value="10"></div>
        </div>
        <div class="field"><label>Catégorie</label>
          <select name="category_id"><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label>Image (nom de fichier)</label><input type="text" name="image" value="logo.svg"></div>
        <div class="field"><label>Description</label><textarea name="description" rows="2"></textarea></div>
        <button class="btn">Ajouter</button>
      </form>
    </div>

    <div class="panel">
      <h2>Importer une image par URL</h2>
      <p class="muted">Récupère l'image d'un produit depuis une adresse web.</p>
      <form method="post" action="/manage">
        <input type="hidden" name="action" value="import_image">
        <div class="field"><label>URL de l'image</label><input type="text" name="image_url" placeholder="http://exemple.com/photo.jpg"></div>
        <button class="btn btn-ghost">Importer</button>
      </form>
      <?php if ($preview !== null): ?>
        <pre style="white-space:pre-wrap;background:var(--surface-2);padding:10px;border-radius:8px;max-height:220px;overflow:auto;font-size:.8rem;margin-top:10px"><?= e($preview) ?></pre>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <h2>Bannière du site</h2>
    <form method="post" action="/manage">
      <input type="hidden" name="action" value="banner">
      <div class="field"><label>Titre</label><input type="text" name="home_title" value="<?= e($banner['title']) ?>"></div>
      <div class="field"><label>Message promo</label><input type="text" name="home_banner" value="<?= e($banner['banner']) ?>"></div>
      <div class="field"><label>Sous-titre</label><input type="text" name="home_subtitle" value="<?= e($banner['subtitle']) ?>"></div>
      <button class="btn">Enregistrer la bannière</button>
    </form>
  </div>

  <div class="panel">
    <h2>Mes produits</h2>
    <?php if (!$myproducts): ?><p class="muted">Aucun produit.</p><?php else: ?>
    <table><thead><tr><th>#</th><th>Nom</th><th>Prix</th><th>Stock</th></tr></thead><tbody>
      <?php foreach ($myproducts as $p): ?>
        <tr><td class="muted"><?= (int)$p['id'] ?></td><td><?= e($p['name']) ?></td>
            <td><?= e(price((int)$p['price'])) ?></td><td><?= (int)$p['stock'] ?></td></tr>
      <?php endforeach; ?>
    </tbody></table>
    <?php endif; ?>
  </div>

  <div class="panel">
    <h2>Utilisateurs</h2>
    <table><thead><tr><th>#</th><th>Nom</th><th>E-mail</th><th>Rôle</th></tr></thead><tbody>
      <?php foreach ($users as $x): ?>
        <tr><td class="muted"><?= (int)$x['id'] ?></td><td><?= e($x['full_name']) ?></td>
            <td class="muted"><?= e($x['email']) ?></td>
            <td><form method="post" action="/manage" style="display:inline">
              <input type="hidden" name="action" value="setrole"><input type="hidden" name="user_id" value="<?= (int)$x['id'] ?>">
              <select name="role" onchange="this.form.submit()">
                <?php foreach (['customer','seller','support','admin'] as $r): ?><option value="<?= $r ?>"<?= $x['role']===$r?' selected':'' ?>><?= $r ?></option><?php endforeach; ?>
              </select></form></td></tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>

  <?php if (!empty($payments)): ?>
  <div class="panel">
    <h2>Transactions récentes</h2>
    <table><thead><tr><th>Txn</th><th>Commande</th><th>Moyen</th><th>Titulaire</th><th>Montant</th><th>Statut</th></tr></thead><tbody>
      <?php foreach ($payments as $pm): ?>
        <tr>
          <td class="muted"><a href="/payment/receipt?id=<?= (int)$pm['id'] ?>"><?= e($pm['txn_ref']) ?></a></td>
          <td class="muted"><?= e($pm['order_ref']) ?></td>
          <td><?= e(strtoupper($pm['method'])) ?></td>
          <!-- Titulaire tel que saisi au paiement -->
          <td><?= $pm['pay_name'] ?></td>
          <td><?= e(price((int)$pm['amount'])) ?></td>
          <td><?= e($pm['status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>
  <?php endif; ?>
</div>
