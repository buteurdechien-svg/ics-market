<div class="wrap">
  <h1>Bonjour, <?= e(explode(' ', $user['full_name'])[0]) ?></h1>
  <div style="display:grid;grid-template-columns:1fr 2fr;gap:22px">
    <div class="panel">
      <h2>Mon profil</h2>
      <?php if (!empty($user['avatar'])): ?>
        <img src="/uploads/<?= e($user['avatar']) ?>" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:10px">
      <?php endif; ?>
      <p><strong><?= e($user['full_name']) ?></strong><br>
         <span class="muted"><?= e($user['email']) ?></span><br>
         <span class="badge badge-role"><?= e($user['role']) ?></span></p>
      <a href="/profile" class="btn btn-ghost">Modifier mon profil</a>
    </div>
    <div class="panel">
      <h2>Mes dernières commandes</h2>
      <?php if (!$orders): ?>
        <p class="muted">Aucune commande pour le moment.</p>
      <?php else: ?>
      <table>
        <thead><tr><th>Référence</th><th>Date</th><th>Total</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= e($o['reference']) ?></td>
            <td class="muted"><?= e(date('d/m/Y', strtotime($o['created_at']))) ?></td>
            <td><?= e(price((int)$o['total'])) ?></td>
            <td><a href="/order/<?= (int)$o['id'] ?>">Détail →</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <p class="mb0"><a href="/orders">Voir toutes mes commandes →</a></p>
      <?php endif; ?>
    </div>
  </div>
</div>
