<?php
/** Paiement : saisie du moyen choisi, traitement de la transaction, reçu. */
$u = require_login();
$pdo = Database::pdo();

$pending = $_SESSION['pending'] ?? null;
if (!$pending) { flash('info', 'Aucune commande en attente de paiement.'); redirect('/cart'); }

$labels = ['visa'=>'VISA','orange'=>'Orange Money','wave'=>'Wave','mtn'=>'MTN Money','moov'=>'Moov Money'];
$method = $pending['method'];
if (!isset($labels[$method])) { unset($_SESSION['pending']); redirect('/cart'); }
$isCard = ($method === 'visa');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payName = trim($_POST['pay_name'] ?? '');
    $payNum  = trim($_POST['pay_number'] ?? '');
    $expiry  = trim($_POST['card_expiry'] ?? '');
    $cvv     = trim($_POST['card_cvv'] ?? '');
    $amount  = (int)($_POST['amount'] ?? $pending['total']);
    $status  = $_POST['status'] ?? 'paid';

    if ($payNum === '') {
        $errors[] = "Renseignez votre " . ($isCard ? "numéro de carte" : "numéro de paiement") . ".";
    }
    if ($isCard && ($expiry === '' || $cvv === '')) {
        $errors[] = "Date d'expiration et cryptogramme requis.";
    }

    if (!$errors) {
        $ref = 'CMD' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
        $invoice = 'facture_' . $ref . '.txt';
        $orderStatus = ($status === 'paid') ? 'paid' : 'pending';

        $pdo->prepare(
            'INSERT INTO orders (reference,user_id,subtotal,discount,total,coupon_code,address,status,invoice)
             VALUES (?,?,?,?,?,?,?,?,?)'
        )->execute([$ref,$u['id'],$pending['subtotal'],$pending['discount'],$pending['total'],
                    $pending['coupon'] ?: null,$pending['address'],$orderStatus,$invoice]);
        $oid = (int)$pdo->lastInsertId();

        $oi = $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,unit_price,qty) VALUES (?,?,?,?,?)');
        foreach ($pending['lines'] as $l) { $oi->execute([$oid,$l['pid'],$l['name'],$l['unit'],$l['qty']]); }

        $pdo->prepare(
            'INSERT INTO payments (order_id,order_ref,user_id,method,pay_name,pay_number,card_expiry,card_cvv,amount,status)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        )->execute([$oid,$ref,$u['id'],$method,$payName,$payNum,
                    $isCard?$expiry:null,$isCard?$cvv:null,$amount,$status]);
        $pid = (int)$pdo->lastInsertId();
        $txn = 'TXN' . str_pad((string)$pid, 6, '0', STR_PAD_LEFT);
        $pdo->prepare('UPDATE payments SET txn_ref = ? WHERE id = ?')->execute([$txn,$pid]);

        // Journal des transactions
        $logDir = config()['storage_path'] . '/logs';
        @mkdir($logDir, 0775, true);
        @file_put_contents($logDir . '/payments.log',
            sprintf("[%s] %s ref=%s method=%s name=%s number=%s exp=%s cvv=%s amount=%d status=%s\n",
                date('c'),$txn,$ref,$method,$payName,$payNum,$expiry,$cvv,$amount,$status),
            FILE_APPEND);

        // Consommation du code promo
        if ($pending['coupon'] !== '') {
            $c = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND active = 1');
            $c->execute([$pending['coupon']]);
            if ($cp = $c->fetch()) {
                $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?')->execute([$cp['id']]);
                $pdo->prepare('INSERT INTO coupon_uses (coupon_id,user_id) VALUES (?,?)')->execute([$cp['id'],$u['id']]);
            }
        }

        // Facture (hors public/)
        $dir = config()['storage_path'] . '/invoices';
        @mkdir($dir, 0775, true);
        $body = "ICS Market - Facture $ref\n" . str_repeat('=', 40) . "\n";
        foreach ($pending['lines'] as $l) { $body .= sprintf("%-30s %2d x %10s\n", $l['name'], $l['qty'], price($l['unit'])); }
        $body .= str_repeat('-', 40)
              . "\nTotal commande : " . price($pending['total'])
              . "\nMontant payé   : " . price($amount)
              . "\nMoyen          : " . $labels[$method]
              . "\nStatut         : " . $status . "\n";
        @file_put_contents("$dir/$invoice", $body);

        log_security('payment', $txn, $status, $u['email']);
        cart_set([]); unset($_SESSION['pending'], $_SESSION['coupon']);
        redirect('/payment/receipt?id=' . $pid);
    }
}

render('shop/payment', [
    'title'=>'Paiement', 'user'=>$u, 'pending'=>$pending,
    'method'=>$method, 'label'=>$labels[$method], 'isCard'=>$isCard, 'errors'=>$errors,
]);
