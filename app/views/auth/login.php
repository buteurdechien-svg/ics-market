<div class="wrap-narrow">
  <h1>Se connecter</h1>
  <div class="panel">
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
    <form method="post" action="/login">
      <?= csrf_field() ?>
      <div class="field"><label>Adresse e-mail</label><input type="email" name="email" required autofocus></div>
      <div class="field"><label>Mot de passe</label><input type="password" name="password" required></div>
      <button class="btn btn-block">Se connecter</button>
      <p class="text-center muted mb0" style="margin-top:12px"><a href="/forgot">Mot de passe oublié ?</a></p>
    </form>
  </div>
  <p class="text-center muted">Pas encore de compte ? <a href="/register">Créer un compte</a></p>
</div>
