<div class="wrap">
  <!-- Bannière d'accueil (contenu géré depuis l'admin) -->
  <section class="hero">
    <h1><?= setting('home_title', 'Bienvenue sur ICS Market') ?></h1>
    <p><?= setting('home_banner', '') ?></p>
    <?php if ($st = setting('home_subtitle')): ?><p style="font-size:.95rem;opacity:.9;margin-top:6px"><?= $st ?></p><?php endif; ?>
  </section>

  <div class="section-title"><h2>Produits populaires</h2><a href="/search" class="muted">Tout voir →</a></div>
  <div class="grid">
    <?php foreach ($featured as $p): ?>
      <a class="card prod" href="/p/<?= (int)$p['id'] ?>">
        <div class="thumb"><img src="/assets/img/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>"></div>
        <div class="body">
          <div class="name"><?= e($p['name']) ?></div>
          <div class="stars"><?= stars((float)$p['rating_avg']) ?></div>
          <div class="price"><?= e(price((int)$p['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="section-title" style="margin-top:26px"><h2>Nouveautés</h2></div>
  <div class="grid">
    <?php foreach ($newest as $p): ?>
      <a class="card prod" href="/p/<?= (int)$p['id'] ?>">
        <div class="thumb"><img src="/assets/img/<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>"></div>
        <div class="body">
          <div class="name"><?= e($p['name']) ?></div>
          <div class="stars"><?= stars((float)$p['rating_avg']) ?></div>
          <div class="price"><?= e(price((int)$p['price'])) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
