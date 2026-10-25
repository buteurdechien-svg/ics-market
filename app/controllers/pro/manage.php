<?php
/** Espace de gestion : vendeur (produits) et admin (site, utilisateurs). */
$u = require_login();
$pdo = Database::pdo();
$errors = [];
$notice = null;
$preview = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product') {
        $name  = trim($_POST['name'] ?? '');
        $price = (int)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $catId = (int)($_POST['category_id'] ?? 0);
        $desc  = trim($_POST['description'] ?? '');
        $img   = trim($_POST['image'] ?? 'logo.svg');
        if ($name === '' || $price <= 0) { $errors[] = "Nom et prix obligatoires."; }
        else {
            $slug = preg_replace('/[^a-z0-9]+/','-', strtolower($name)) . '-' . bin2hex(random_bytes(2));
            $pdo->prepare('INSERT INTO products (category_id,seller_id,name,slug,description,price,stock,image)
                           VALUES (?,?,?,?,?,?,?,?)')
                ->execute([$catId ?: null, $u['id'], $name, $slug, $desc, $price, $stock, $img]);
            $notice = "Produit ajouté.";
        }
    }
    elseif ($action === 'import_image') {
        // Import d'une image produit depuis une URL (récupérée côté serveur).
        $url = trim($_POST['image_url'] ?? '');
        if ($url !== '') {
            $ctx = stream_context_create(['http' => ['timeout' => 3]]);
            $data = @file_get_contents($url, false, $ctx);
            $preview = $data === false ? "(aucune réponse)" : substr($data, 0, 2000);
        }
    }
    elseif ($action === 'banner') {
        // Mise à jour de la bannière d'accueil.
        $pdo->prepare('UPDATE site_settings SET sval=? WHERE skey="home_title"')->execute([$_POST['home_title'] ?? '']);
        $pdo->prepare('UPDATE site_settings SET sval=? WHERE skey="home_banner"')->execute([$_POST['home_banner'] ?? '']);
        $pdo->prepare('UPDATE site_settings SET sval=? WHERE skey="home_subtitle"')->execute([$_POST['home_subtitle'] ?? '']);
        log_security('banner_update', 'home', 'done', $u['email']);
        $notice = "Bannière mise à jour.";
    }
    elseif ($action === 'setrole') {
        $uid = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? 'customer';
        if (in_array($role, ['customer','seller','support','admin'], true)) {
            $pdo->prepare('UPDATE users SET role=? WHERE id=?')->execute([$role, $uid]);
            $notice = "Rôle mis à jour.";
        }
    }
}

$data = ['title'=>'Gestion','nav'=>'','user'=>$u,'errors'=>$errors,'notice'=>$notice,'preview'=>$preview];
$data['categories'] = $pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$data['myproducts'] = $pdo->prepare('SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC');
$data['myproducts']->execute([$u['id']]); $data['myproducts'] = $data['myproducts']->fetchAll();
$data['banner'] = ['title'=>setting('home_title'),'banner'=>setting('home_banner'),'subtitle'=>setting('home_subtitle')];
$data['users'] = $pdo->query('SELECT id,full_name,email,role FROM users ORDER BY id')->fetchAll();
render('pro/manage', $data);
