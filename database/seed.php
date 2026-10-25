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
foreach (['coupon_uses','coupons','order_items','orders','cart_items','carts','reviews',
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
$products = [
    // [cat, name, price, stock, image, desc]
    ['electronique','Smartphone ICS X1 128 Go', 129900, 40, 'phone.svg',    'Écran 6,5", double SIM, batterie 5000 mAh. Parfait pour le quotidien.'],
    ['electronique','Ordinateur portable ICS Book 14"', 489000, 15, 'laptop.svg', '8 Go RAM, 256 Go SSD, idéal bureautique et études.'],
    ['electronique','Téléviseur LED 43 pouces', 189000, 12, 'tv.svg',       'Full HD, HDMI x3, son clair. Grand écran pour le salon.'],
    ['electronique','Casque audio sans fil ICS Beat', 24900, 80, 'headset.svg','Bluetooth 5.3, réduction de bruit, autonomie 30h.'],
    ['accessoires','Powerbank 20000 mAh', 15900, 120, 'powerbank.svg',       'Charge rapide 22,5W, 2 ports USB. Ne tombez plus en panne.'],
    ['accessoires','Montre connectée ICS Fit', 34900, 60, 'watch.svg',       'Suivi activité, notifications, étanche. Autonomie 7 jours.'],
    ['maison','Ventilateur sur pied silencieux', 29900, 35, 'fan.svg',       '3 vitesses, oscillation, télécommande. Fraîcheur assurée.'],
    ['maison','Blender multifonction 1,5 L', 22900, 50, 'blender.svg',       'Mixe, broie, mixe les jus. Bol résistant, moteur 500W.'],
    ['mode','Pagne wax premium 6 yards', 15000, 100, 'wax.svg',              'Tissu wax de qualité, couleurs éclatantes. Idéal couture.'],
    ['mode','Baskets urbaines ICS Street', 27900, 70, 'shoe.svg',            'Confortables et légères, semelle antidérapante.'],
    ['beaute','Coffret soin visage naturel', 18900, 45, 'beauty.svg',        'Nettoyant, crème et sérum aux actifs naturels.'],
    ['alimentaire','Riz parfumé premium 25 kg', 21500, 200, 'rice.svg',      'Grain long, parfumé. Le sac familial pour tout le mois.'],
];
$pStmt = $pdo->prepare(
    'INSERT INTO products (category_id,seller_id,name,slug,description,price,stock,image,rating_avg)
     VALUES (?,?,?,?,?,?,?,?,?)'
);
$slugify = fn(string $s) => trim(preg_replace('/[^a-z0-9]+/','-', strtolower(iconv('UTF-8','ASCII//TRANSLIT',$s))),'-');
$prodIds = [];
foreach ($products as $i=>$p) {
    $slug = $slugify($p[1]) . '-' . ($i+1);
    $rating = [4.0,4.5,5.0,3.5,4.0][$i % 5];
    $pStmt->execute([$cid[$p[0]], $sellerId, $p[1], $slug, $p[5], $p[2], $p[3], $p[4], $rating]);
    $prodIds[] = (int)$pdo->lastInsertId();
}

/* ───────── Avis ───────── */
echo "Avis...\n";
$rStmt = $pdo->prepare('INSERT INTO reviews (product_id,user_id,author_name,rating,body) VALUES (?,?,?,?,?)');
$reviews = [
    [0,'ange@ics-market.local','Michael',5,'Super téléphone, livraison rapide à Abidjan. Je recommande !'],
    [0,'fatou@ics-market.local','Fatou',4,'Bon rapport qualité-prix, la batterie tient bien.'],
    [3,'awa@ics-market.local','Awa',5,'Le casque est top, le son est clair.'],
    [8,'fatou@ics-market.local','Fatou',5,'Très beau pagne, couleurs magnifiques.'],
    [11,'yao@ics-market.local','Yao',4,'Bon riz, le sac est costaud.'],
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
