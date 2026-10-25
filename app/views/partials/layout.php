<?php
$cfg  = config();
$user = $user ?? current_user();
$nav  = $nav ?? '';
$cats = $cats ?? Database::pdo()->query('SELECT name, slug FROM categories ORDER BY id')->fetchAll();
$cartN = cart_count();
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'ICS Market') ?> — ICS Market</title>
  <link rel="icon" href="/assets/img/logo.svg">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <div class="lab-banner">⚠️ SITE FICTIF — Laboratoire de formation au pentest. Données synthétiques. Volontairement vulnérable. Usage local uniquement.</div>

  <header class="top">
    <div class="bar">
      <a class="brand" href="/">
        <img src="/assets/img/logo.svg" width="30" height="30" alt="">
        <span class="mark">ICS<b>Market</b></span>
      </a>
      <form class="search" action="/search" method="get">
        <input type="search" name="q" placeholder="Rechercher un produit, une marque..." value="<?= e($q ?? '') ?>">
        <button type="submit">Rechercher</button>
      </form>
      <nav class="links">
        <?php if ($user): ?>
          <a href="/account">👤 <?= e(explode(' ', $user['full_name'])[0]) ?></a>
          <?php if ($user['role'] !== 'customer'): ?><a href="/manage"><?= e($user['role']) ?></a><?php endif; ?>
          <a href="/logout">Déconnexion</a>
        <?php else: ?>
          <a href="/login">👤 Se connecter</a>
        <?php endif; ?>
        <a href="/cart">🛒 Panier <?php if ($cartN): ?><span class="cart-badge"><?= $cartN ?></span><?php endif; ?></a>
      </nav>
    </div>
  </header>

  <nav class="catnav"><div class="inner">
    <a href="/"<?= $nav===''?' class="active"':'' ?>>Accueil</a>
    <?php foreach ($cats as $c): ?>
      <a href="/c/<?= e($c['slug']) ?>"<?= ($nav===$c['slug'])?' class="active"':'' ?>><?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div></nav>

  <?php foreach (take_flash() as $f): ?>
    <div class="wrap" style="padding-bottom:0">
      <div class="alert alert-<?= $f['type']==='error'?'error':($f['type']==='ok'?'ok':'info') ?>"><?= e($f['msg']) ?></div>
    </div>
  <?php endforeach; ?>

  <?= $content ?>

  <div class="footer">
    ICS Market — Ivoire Cyber Shield · Marketplace fictive de formation · © <?= date('Y') ?><br>
    Données exclusivement synthétiques. Aucune vente réelle.
  </div>
</body>
</html>
