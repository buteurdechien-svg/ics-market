<?php
/**
 * ICS Market — remplissage des données de démonstration.
 * Tout est fictif. Les mots de passe sont hachés ici.
 */
declare(strict_types=1);

require dirname(__DIR__) . '/app/lib/Database.php';
$pdo = Database::pdo();

echo "Nettoyage...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['payments','coupon_uses','coupons','order_items','orders','cart_items','carts','reviews',
          'product_images','products','categories','addresses','otp_challenges',
          'auth_events','security_events','site_settings','users'] as $t) {
    $pdo->exec("TRUNCATE TABLE $t");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$hash = fn(string $s): string => password_hash($s, PASSWORD_BCRYPT);

/* ───────── Utilisateurs ───────── */
echo "Utilisateurs...\n";
$users = [
    // [full_name, email, phone, pass, role]
    ['Administrateur ICS',    'admin@ics-market.local',   '+2250700000000', 'volkoff007@',  'admin'],
    ['Vendeur Pro ICS',       'seller@ics-market.local',  '+2250700000010', 'Seller#2026',  'seller'],
    ['Support ICS',           'support@ics-market.local', '+2250700000011', 'SupportICS#26','support'],
    ['Michael Ange Ahizé',    'ange@ics-market.local',    '+2250700000001', 'Ange#2026',    'customer'],
    ['Fatou Koné',            'fatou@ics-market.local',   '+2250700000002', 'Fatou2025!',   'customer'],
    ['Yao Kouassi',           'yao@ics-market.local',     '+2250700000003', 'kouassi123',   'customer'], // faible
    ['Awa Traoré',            'awa@ics-market.local',     '+2250700000004', 'Awa#Traore7',  'customer'],
];
$uStmt = $pdo->prepare('INSERT INTO users (full_name,email,phone,pass_hash,role,is_seeded) VALUES (?,?,?,?,?,1)');
$uid = [];
foreach ($users as $u) {
    $uStmt->execute([$u[0],$u[1],$u[2],$hash($u[3]),$u[4]]);
    $uid[$u[1]] = (int)$pdo->lastInsertId();
}
$sellerId = $uid['seller@ics-market.local'];

/* ───────── Catégories ───────── */
echo "Catégories...\n";
$cats = [
    'Électronique'=>'electronique', 'Mode'=>'mode', 'Maison'=>'maison',
    'Beauté'=>'beaute', 'Alimentaire'=>'alimentaire', 'Accessoires'=>'accessoires',
];
$cStmt = $pdo->prepare('INSERT INTO categories (name,slug) VALUES (?,?)');
$cid = [];
foreach ($cats as $name=>$slug) { $cStmt->execute([$name,$slug]); $cid[$slug] = (int)$pdo->lastInsertId(); }

/* ───────── Produits ───────── */
echo "Produits...\n";
// [cat, brand, name, price, stock, image, desc, specs]  (marques et données fictives)
$products = require __DIR__ . '/products.php';

$pStmt = $pdo->prepare(
    'INSERT INTO products (category_id,seller_id,brand,name,slug,description,specs,price,stock,image,rating_avg)
     VALUES (?,?,?,?,?,?,?,?,?,?,?)'
);
$slugify = fn(string $s) => trim(preg_replace('/[^a-z0-9]+/','-', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$s))),'-');
$prodIds = [];
foreach ($products as $i=>$p) {
    [$cat,$brand,$name,$price,$stock,$image,$desc,$specs] = $p;
    $slug = $slugify($name) . '-' . ($i+1);
    $rating = [4.0,4.5,5.0,4.5,4.0,3.5,5.0][$i % 7];
    $pStmt->execute([$cid[$cat], $sellerId, $brand, $name, $slug, $desc, $specs, $price, $stock, $image, $rating]);
    $prodIds[] = (int)$pdo->lastInsertId();
}

/* ───────── Avis ───────── */
echo "Avis...\n";
$rStmt = $pdo->prepare('INSERT INTO reviews (product_id,user_id,author_name,rating,body) VALUES (?,?,?,?,?)');
$reviews = [
    [0,'ange@ics-market.local','Michael',5,'Super téléphone, livraison rapide à Abidjan. Je recommande !'],
    [0,'fatou@ics-market.local','Fatou',4,'Bon rapport qualité-prix, la batterie tient bien.'],
    [21,'awa@ics-market.local','Awa',5,'Les écouteurs sont top, le son est clair.'],
    [60,'fatou@ics-market.local','Fatou',5,'Très beau pagne, couleurs magnifiques.'],
    [100,'yao@ics-market.local','Yao',4,'Bon riz, le sac est costaud.'],
    [80,'ange@ics-market.local','Michael',5,'Le coffret soin est complet, peau douce.'],
    [24,'awa@ics-market.local','Awa',4,'Montre pratique, autonomie correcte.'],
];
foreach ($reviews as $r) {
    $rStmt->execute([$prodIds[$r[0]], $uid[$r[1]], $r[2], $r[3], $r[4]]);
}

/* ───────── Coupons ───────── */
echo "Coupons...\n";
$cpStmt = $pdo->prepare('INSERT INTO coupons (code,type,value,max_uses,used_count,active) VALUES (?,?,?,?,0,1)');
$cpStmt->execute(['BIENVENUE10','percent',10,1]);
$cpStmt->execute(['PROMO5000','amount',5000,1]);
$cpStmt->execute(['VIP20','percent',20,1]);

/* ───────── Bannière d'accueil (défiguration) ───────── */
echo "Réglages du site...\n";
$ss = $pdo->prepare('INSERT INTO site_settings (skey,sval) VALUES (?,?)');
$ss->execute(['home_title', 'Bienvenue sur ICS Market']);
$ss->execute(['home_banner', 'Grande promo de lancement — jusqu\'à -50% sur l\'électronique !']);
$ss->execute(['home_subtitle', 'Des milliers de produits livrés partout en Côte d\'Ivoire.']);

echo "Terminé. Catalogue ICS Market prêt.\n";
