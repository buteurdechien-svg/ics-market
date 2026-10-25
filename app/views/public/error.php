<div class="wrap-narrow text-center" style="padding-top:50px">
  <div class="panel">
    <h1 style="font-size:3rem;margin:0"><?= e((string)($code ?? 404)) ?></h1>
    <p class="muted"><?= e($message ?? 'Une erreur est survenue.') ?></p>
    <a href="/" class="btn">Retour à l'accueil</a>
  </div>
</div>
