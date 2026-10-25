<div class="wrap-narrow">
  <h1>Créer un compte</h1>
  <p class="muted">Rejoignez ICS Market en une minute.</p>
  <div class="panel">
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
    <form method="post" action="/register">
      <?= csrf_field() ?>
      <div class="field"><label>Nom complet</label><input type="text" name="full_name" value="<?= old_input('full_name') ?>" required></div>
      <div class="field"><label>Adresse e-mail</label><input type="email" name="email" value="<?= old_input('email') ?>" required></div>
      <div class="field"><label>Téléphone</label><input type="tel" name="phone" value="<?= old_input('phone') ?>" placeholder="+225 07 00 00 00 00" required>
        <div class="help">Numéro fictif accepté pour la formation.</div></div>
      <div class="field"><label>Mot de passe</label><input type="password" name="password" minlength="6" required></div>
      <div class="field"><label>Confirmer</label><input type="password" name="password2" minlength="6" required></div>
      <button class="btn btn-block">Recevoir mon code</button>
    </form>
  </div>
  <p class="text-center muted">Déjà client ? <a href="/login">Se connecter</a></p>
</div>
<?php unset($_SESSION['_old']); ?>
