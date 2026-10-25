<div class="wrap-narrow">
  <h1>Mot de passe oublié</h1>
  <p class="muted">Saisissez votre e-mail pour recevoir un code de réinitialisation.</p>
  <div class="panel">
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
    <?php if ($info): ?><div class="alert alert-ok"><?= e($info) ?></div><?php endif; ?>
    <form method="post" action="/forgot">
      <?= csrf_field() ?>
      <div class="field"><label>Adresse e-mail</label><input type="email" name="email" required autofocus></div>
      <button class="btn btn-block">Recevoir un code</button>
    </form>
  </div>
  <p class="text-center muted"><a href="/login">Retour à la connexion</a></p>
</div>
