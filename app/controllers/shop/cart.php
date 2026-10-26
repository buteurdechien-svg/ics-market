<?php
/** Panier (réservé aux clients connectés). */
require_login();
$pdo = Database::pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $cart = cart_get();
    if ($action === 'update') {
        foreach (($_POST['qty'] ?? []) as $pid => $q) {
            $pid = (int)$pid; $q = (int)$q;
            if ($q === 0) { unset($cart[$pid]); } else { $cart[$pid] = $q; }
        }
        cart_set($cart);
        flash('ok', 'Panier mis à jour.');
    } elseif ($action === 'remove') {
        unset($cart[(int)($_POST['product_id'] ?? 0)]);
        cart_set($cart);
    } elseif ($action === 'coupon') {
        $_SESSION['coupon'] = trim($_POST['coupon'] ?? '');
        flash('info', 'Code promo appliqué.');
    }
    redirect('/cart');
}

$cart = cart_get();
$items = [];
$subtotal = 0;
if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $rows = $pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();
    foreach ($rows as $p) {
        $qty = (int)$cart[$p['id']];
        $line = (int)$p['price'] * $qty;
        $subtotal += $line;
        $items[] = ['p'=>$p, 'qty'=>$qty, 'line'=>$line];
    }
}
render('shop/cart', ['title'=>'Mon panier','items'=>$items,'subtotal'=>$subtotal,'coupon'=>$_SESSION['coupon'] ?? '']);
