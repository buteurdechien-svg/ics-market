<div class="wrap">
  <h1>Mes commandes</h1>
  <div class="panel">
    <?php if (!$orders): ?><p class="muted">Aucune commande.</p><?php else: ?>
    <table>
      <thead><tr><th>Référence</th><th>Date</th><th>Articles</th><th>Total</th><th>Statut</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><?= e($o['reference']) ?></td>
          <td class="muted"><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td>
          <td class="muted">—</td>
          <td><?= e(price((int)$o['total'])) ?></td>
          <td><span class="badge badge-role"><?= e($o['status']) ?></span></td>
          <td><a href="/order/<?= (int)$o['id'] ?>">Détail →</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
