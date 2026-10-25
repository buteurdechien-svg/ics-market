<div class="wrap">
  <h1>Mon profil</h1>
  <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:22px">
    <div class="panel">
      <h2>Informations</h2>
      <form method="post" action="/profile">
        <?= csrf_field() ?><input type="hidden" name="action" value="info">
        <div class="field"><label>Nom complet</label><input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required></div>
        <div class="field"><label>E-mail</label><input type="email" value="<?= e($user['email']) ?>" disabled></div>
        <div class="field"><label>Téléphone</label><input type="tel" name="phone" value="<?= e($user['phone']) ?>"></div>
        <button class="btn">Enregistrer</button>
      </form>
    </div>
    <div>
      <div class="panel">
        <h2>Photo de profil</h2>
        <?php if (!empty($user['avatar'])): ?>
          <img src="/uploads/<?= e($user['avatar']) ?>" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:10px">
        <?php endif; ?>
        <form method="post" action="/profile" enctype="multipart/form-data">
          <input type="hidden" name="action" value="avatar">
          <div class="field"><input type="file" name="avatar" required></div>
          <button class="btn btn-ghost">Téléverser</button>
        </form>
      </div>
      <div class="panel">
        <h2>Mot de passe</h2>
        <form method="post" action="/profile">
          <?= csrf_field() ?><input type="hidden" name="action" value="password">
          <div class="field"><label>Actuel</label><input type="password" name="current" required></div>
          <div class="field"><label>Nouveau</label><input type="password" name="new" minlength="6" required></div>
          <div class="field"><label>Confirmer</label><input type="password" name="new2" minlength="6" required></div>
          <button class="btn">Changer</button>
        </form>
      </div>
    </div>
  </div>
</div>
