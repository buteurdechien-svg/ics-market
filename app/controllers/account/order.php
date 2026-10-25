<?php
/** Détail d'une commande. */
$u = require_login();
$pdo = Database::pdo();
$id = (int)($_GET['id'] ?? 0);

$o = $pdo->prepare('SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE o.id = ?');
$o->execute([$id]);
$order = $o->fetch();
if (!$order) { http_response_code(404); render('public/error',['code'=>404,'message'=>'Commande introuvable.','title'=>'Introuvable']); exit; }

$it = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$it->execute([$id]);
render('account/order', ['title'=>'Commande '.$order['reference'],'user'=>$u,'order'=>$order,'items'=>$it->fetchAll()]);
