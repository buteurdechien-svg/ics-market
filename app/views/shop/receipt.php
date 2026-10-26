<?php
  $num  = (string)($pay['pay_number'] ?? '');
  $last = $num !== '' ? substr($num, -4) : '';
  $mask = $last !== '' ? str_repeat('•', max(0, strlen($num) - 4)) . $last : '—';
  $statusOk = ($pay['status'] === 'paid');
?>
<div class="wrap">
  <h1>Reçu de paiement</h1>

  <div class="panel" style="max-width:640px">
    <div class="alert alert-<?= $statusOk ? 'ok' : 'info' ?>">
      <?= $statusOk ? 'Paiement confirmé.' : 'Paiement en attente.' ?>
    </div>

    <table>
      <tr><th style="width:42%">Transaction</th><td><?= e($pay['txn_ref']) ?></td></tr>
      <tr><th>Commande</th><td><?= e($pay['order_ref']) ?></td></tr>
      <!-- Nom du titulaire tel que saisi -->
      <tr><th>Titulaire</th><td><?= $pay['pay_name'] ?></td></tr>
      <tr><th>Moyen de paiement</th><td><?= e(strtoupper($pay['method'])) ?></td></tr>
      <tr><th>Numéro</th><td><?= e($mask) ?></td></tr>
      <tr><th>Montant payé</th><td><?= e(price((int)$pay['amount'])) ?></td></tr>
      <tr><th>Statut</th><td><?= e($pay['status']) ?></td></tr>
      <tr><th>Date</th><td><?= e(date('d/m/Y H:i', strtotime($pay['created_at']))) ?></td></tr>
    </table>

    <p style="margin-top:16px">
      <?php if (!empty($pay['order_id'])): ?>
        <a href="/order/<?= (int)$pay['order_id'] ?>" class="btn">Voir ma commande</a>
      <?php endif; ?>
      <a href="/" class="btn btn-ghost">Continuer mes achats</a>
    </p>
  </div>
</div>
