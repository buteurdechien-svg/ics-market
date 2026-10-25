<div class="wrap-narrow">
  <h1>Vérification</h1>
  <p class="muted">Un code à 6 chiffres a été envoyé pour le numéro <strong><?= e($phone) ?></strong>.
     Dans ce laboratoire, retrouvez-le dans <strong>Mailpit</strong> (http://&lt;ip&gt;:8025).</p>
  <div class="panel">
    <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= e($e) ?></div><?php endforeach; ?>
    <form method="post" action="/verify">
      <?= csrf_field() ?>
      <div class="field"><label>Code de vérification</label>
        <input type="text" name="code" inputmode="numeric" maxlength="6" required autofocus></div>
      <button class="btn btn-block">Valider</button>
    </form>
  </div>
  <p class="text-center muted"><a href="/register">Recommencer</a></p>
</div>
