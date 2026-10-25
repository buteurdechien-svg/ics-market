<?php
/** Espace compte client. */
$u = require_login();
$pdo = Database::pdo();
$o = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
$o->execute([$u['id']]);
render('account/account', ['title'=>'Mon compte','user'=>$u,'orders'=>$o->fetchAll()]);
