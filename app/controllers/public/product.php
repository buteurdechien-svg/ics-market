<?php
/** Fiche produit : détail, avis, ajout au panier. */
$pdo = Database::pdo();
$id = (int)($_GET['id'] ?? 0);

$ps = $pdo->prepare('SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p
                     LEFT JOIN categories c ON c.id=p.category_id WHERE p.id = ?');
$ps->execute([$id]);
$product = $ps->fetch();
if (!$product) { http_response_code(404); render('public/error',['code'=>404,'message'=>'Produit introuvable.','title'=>'Introuvable']); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_cart') {
        $qty = (int)($_POST['qty'] ?? 1);
        $cart = cart_get();
        $cart[$id] = ($cart[$id] ?? 0) + max(1, $qty);
        cart_set($cart);
        flash('ok', 'Produit ajouté au panier.');
        redirect('/p/' . $id);
    }
    if ($action === 'review') {
        $name = trim($_POST['author_name'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $u = current_user();
        if ($name === '' || $body === '') {
            $errors[] = "Nom et commentaire obligatoires.";
        } else {
            $pdo->prepare('INSERT INTO reviews (product_id,user_id,author_name,rating,body) VALUES (?,?,?,?,?)')
                ->execute([$id, $u['id'] ?? null, $name, $rating, $body]);
            flash('ok', 'Merci pour votre avis !');
            redirect('/p/' . $id);
        }
    }
}

$rv = $pdo->prepare('SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC');
$rv->execute([$id]);
render('public/product', [
    'title'=>$product['name'], 'nav'=>$product['cat_slug'],
    'product'=>$product, 'reviews'=>$rv->fetchAll(), 'errors'=>$errors,
]);
