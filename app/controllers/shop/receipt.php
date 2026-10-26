<?php
/** Reçu de paiement, consultable par identifiant de transaction. */
$u = require_login();
$pdo = Database::pdo();

$id = (int)($_GET['id'] ?? 0);
$ps = $pdo->prepare('SELECT * FROM payments WHERE id = ?');
$ps->execute([$id]);
$pay = $ps->fetch();
if (!$pay) {
    http_response_code(404);
    render('public/error', ['code'=>404,'message'=>'Reçu introuvable.','title'=>'Introuvable']);
    exit;
}
render('shop/receipt', ['title'=>'Reçu ' . $pay['txn_ref'], 'pay'=>$pay, 'user'=>$u]);
