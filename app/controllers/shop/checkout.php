<?php
/** Checkout : récapitulatif, adresse et choix du moyen de paiement. */
$u = require_login();
$pdo = Database::pdo();
$cart = cart_get();
if (!$cart) { flash('info', 'Votre panier est vide.'); redirect('/cart'); }

$ids = implode(',', array_map('intval', array_keys($cart)));
$products = [];
foreach ($pdo->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll() as $p) { $products[(int)$p['id']] = $p; }

$methods = [
    'visa'   => ['label'=>'VISA',         'kind'=>'card',   'tag'=>'Carte bancaire'],
    'orange' => ['label'=>'Orange Money', 'kind'=>'mobile', 'tag'=>'Mobile money'],
    'wave'   => ['label'=>'Wave',         'kind'=>'mobile', 'tag'=>'Mobile money'],
    'mtn'    => ['label'=>'MTN Money',    'kind'=>'mobile', 'tag'=>'Mobile money'],
    'moov'   => ['label'=>'Moov Money',   'kind'=>'mobile', 'tag'=>'Mobile money'],
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $method  = $_POST['method'] ?? '';
    if (!csrf_check())             { $errors[] = "Session expirée, réessayez."; }
    if ($address === '')          { $errors[] = "Indiquez une adresse de livraison."; }
    if (!isset($methods[$method])){ $errors[] = "Choisissez un moyen de paiement."; }

    if (!$errors) {
        // Lignes : le prix unitaire transmis par le formulaire est utilisé.
        $lines = []; $subtotal = 0;
        foreach ($cart as $pid => $qty) {
            $pid = (int)$pid;
            if (!isset($products[$pid])) continue;
            $qty  = (int)($_POST['qty'][$pid] ?? $qty);
            $unit = (int)($_POST['unit_price'][$pid] ?? $products[$pid]['price']);
            $subtotal += $unit * $qty;
            $lines[] = ['pid'=>$pid, 'name'=>$products[$pid]['name'], 'unit'=>$unit, 'qty'=>$qty];
        }
        $discount = 0; $couponCode = $_SESSION['coupon'] ?? '';
        if ($couponCode !== '') {
            $c = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND active = 1');
            $c->execute([$couponCode]);
            if ($cp = $c->fetch()) {
                $discount = $cp['type'] === 'percent'
                    ? (int)round($subtotal * $cp['value'] / 100)
                    : (int)$cp['value'];
            }
        }
        $total = max(0, $subtotal - $discount);
        $_SESSION['pending'] = [
            'address'=>$address, 'method'=>$method, 'lines'=>$lines,
            'subtotal'=>$subtotal, 'discount'=>$discount, 'total'=>$total, 'coupon'=>$couponCode,
        ];
        redirect('/payment');
    }
}

// Récapitulatif pour l'affichage (prix DB)
$items = []; $subtotal = 0;
foreach ($cart as $pid=>$qty) {
    $pid=(int)$pid; if(!isset($products[$pid])) continue;
    $line=(int)$products[$pid]['price']*(int)$qty; $subtotal+=$line;
    $items[] = ['p'=>$products[$pid],'qty'=>(int)$qty,'line'=>$line];
}
render('shop/checkout', ['title'=>'Commander','user'=>$u,'items'=>$items,'subtotal'=>$subtotal,
    'coupon'=>$_SESSION['coupon'] ?? '','errors'=>$errors,'methods'=>$methods]);
