<?php
/** Liste des commandes du client. */
$u = require_login();
$pdo = Database::pdo();
$o = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$o->execute([$u['id']]);
render('account/orders', ['title'=>'Mes commandes','user'=>$u,'orders'=>$o->fetchAll()]);
