<?php
/** Checkout : récapitulatif, paiement simulé, création de commande. */
$u = require_login();
$pdo = Database::pdo();
$cart = cart_get();
if (!$cart) { flash('info', 'Votre panier est vide.'); redirect('/cart'); }

$ids = implode(',', array_map('intval', array_keys($cart)));
$products = [];
foreach ($pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll() as $p) { $products[(int)$p['id']] = $p; }

$errors = [];
$receipt = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    if (!csrf_check())        { $errors[] = "Session expirée, réessayez."; }
    if ($address === '')      { $errors[] = "Indiquez une adresse de livraison."; }

    // Lignes de commande : le prix unitaire transmis par le formulaire est utilisé.
    $lines = [];
    $subtotal = 0;
    foreach ($cart as $pid => $qty) {
        $pid = (int)$pid;
        if (!isset($products[$pid])) continue;
        $qty  = (int)($_POST['qty'][$pid] ?? $qty);
        $unit = (int)($_POST['unit_price'][$pid] ?? $products[$pid]['price']);
        $line = $unit * $qty;
        $subtotal += $line;
        $lines[] = ['pid'=>$pid, 'name'=>$products[$pid]['name'], 'unit'=>$unit, 'qty'=>$qty];
    }

    // Code promo : on applique la remise correspondante.
    $discount = 0;
    $couponCode = $_SESSION['coupon'] ?? '';
    if ($couponCode !== '') {
        $c = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND active = 1');
        $c->execute([$couponCode]);
        if ($cp = $c->fetch()) {
            $discount = $cp['type'] === 'percent'
                ? (int)round($subtotal * $cp['value'] / 100)
                : (int)$cp['value'];
            $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?')->execute([$cp['id']]);
            $pdo->prepare('INSERT INTO coupon_uses (coupon_id,user_id) VALUES (?,?)')->execute([$cp['id'],$u['id']]);
        }
    }

    $total = $subtotal - $discount;

    if (!$errors) {
        $ref = 'CMD' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
        $invoice = 'facture_' . $ref . '.txt';
        $pdo->prepare(
            'INSERT INTO orders (reference,user_id,subtotal,discount,total,coupon_code,address,status,invoice)
             VALUES (?,?,?,?,?,?,?,"paid",?)'
        )->execute([$ref,$u['id'],$subtotal,$discount,$total,$couponCode ?: null,$address,$invoice]);
        $oid = (int)$pdo->lastInsertId();
        $oi = $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,unit_price,qty) VALUES (?,?,?,?,?)');
        foreach ($lines as $l) { $oi->execute([$oid,$l['pid'],$l['name'],$l['unit'],$l['qty']]); }

        // Facture écrite dans storage/invoices (hors public/)
        $dir = config()['storage_path'] . '/invoices';
        @mkdir($dir, 0775, true);
        $body = "ICS Market - Facture $ref\n" . str_repeat('=', 40) . "\n";
        foreach ($lines as $l) { $body .= sprintf("%-30s %2d x %10s\n", $l['name'], $l['qty'], price($l['unit'])); }
        $body .= str_repeat('-', 40) . "\nSous-total : " . price($subtotal) . "\nRemise     : -" . price($discount) . "\nTotal      : " . price($total) . "\n";
        @file_put_contents("$dir/$invoice", $body);

        log_security('order', $ref, 'paid', $u['email']);
        cart_set([]); unset($_SESSION['coupon']);
        $receipt = ['ref'=>$ref,'total'=>$total,'oid'=>$oid,'invoice'=>$invoice];
    }
}

// Récapitulatif pour l'affichage (prix DB)
$items = [];
$subtotal = 0;
foreach ($cart as $pid=>$qty) {
    $pid=(int)$pid; if(!isset($products[$pid])) continue;
    $line=(int)$products[$pid]['price']*(int)$qty; $subtotal+=$line;
    $items[] = ['p'=>$products[$pid],'qty'=>(int)$qty,'line'=>$line];
}
render('shop/checkout', ['title'=>'Commander','user'=>$u,'items'=>$items,'subtotal'=>$subtotal,
    'coupon'=>$_SESSION['coupon'] ?? '','errors'=>$errors,'receipt'=>$receipt]);
