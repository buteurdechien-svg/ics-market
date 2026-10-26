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
// [cat, brand, name, price, stock, image, desc, specs]  (marques et données fictives)
$products = [
  // ───── Électronique ─────
  ['electronique','Nexora','Nexora X1 — Smartphone 128 Go', 129900, 40, 'phone.svg', 'Smartphone 4G double SIM au design fin, parfait pour le quotidien.', "Écran : 6,5\" HD+ 90Hz\nMémoire : 6 Go RAM / 128 Go\nBatterie : 5000 mAh, charge 18W\nPhoto : 50 Mpx + 2 Mpx\nOS : Android 14\nMatériaux : verre et polycarbonate\nGarantie : 12 mois"],
  ['electronique','Nexora','Nexora X1 Pro — Smartphone 256 Go', 189900, 25, 'phone.svg', 'La version Pro, plus de mémoire et un meilleur capteur photo.', "Écran : 6,7\" AMOLED 120Hz\nMémoire : 8 Go RAM / 256 Go\nBatterie : 5200 mAh, charge 33W\nPhoto : 108 Mpx OIS\nOS : Android 14\nGarantie : 12 mois"],
  ['electronique','Nexora','Nexora Book 14 — Ordinateur portable', 489000, 15, 'laptop.svg', 'Portable léger pour bureautique, études et télétravail.', "Écran : 14\" Full HD IPS\nProcesseur : quad-core 2,8 GHz\nMémoire : 8 Go RAM / 256 Go SSD\nAutonomie : jusqu'à 9h\nPoids : 1,4 kg\nConnectique : USB-C, HDMI, Wi-Fi 6\nGarantie : 24 mois"],
  ['electronique','Pixaro','Pixaro V43 — TV LED 43\"', 189000, 18, 'tv.svg', 'Téléviseur Full HD pour le salon, image nette et son clair.', "Dalle : 43\" LED Full HD\nHDMI : x3, USB : x2\nSon : 2 x 10W\nSmart TV : oui (Wi-Fi)\nClasse énergie : E\nGarantie : 24 mois"],
  ['electronique','Pixaro','Pixaro V55 — TV UHD 55\"', 329000, 10, 'tv.svg', 'Grand écran 4K UHD avec contrastes profonds.', "Dalle : 55\" UHD 4K\nHDR10 : oui\nHDMI : x4\nSon : 2 x 12W\nSmart TV : oui\nGarantie : 24 mois"],
  ['electronique','Nexora','Nexora Tab 10 — Tablette 10\"', 139900, 30, 'tablet.svg', 'Tablette polyvalente pour le divertissement et les enfants.', "Écran : 10,1\" IPS\nMémoire : 4 Go RAM / 64 Go (extensible)\nBatterie : 6000 mAh\nOS : Android 14\nGarantie : 12 mois"],
  ['electronique','Soniq','Soniq Pods — Écouteurs sans fil', 29900, 90, 'earbuds.svg', 'Écouteurs true wireless avec réduction de bruit.', "Bluetooth : 5.3\nRéduction de bruit active\nAutonomie : 6h (+24h boîtier)\nÉtanchéité : IPX4\nGarantie : 12 mois"],
  ['electronique','Pixaro','Pixaro Shot 20 — Appareil photo', 215000, 12, 'camera.svg', 'Appareil photo compact 20 Mpx pour débuter en photographie.', "Capteur : 20 Mpx\nZoom optique : x5\nVidéo : Full HD 60fps\nÉcran : 3\" orientable\nGarantie : 24 mois"],
  ['electronique','Volt','Volt Wave — Routeur Wi-Fi 6', 45900, 40, 'router.svg', 'Routeur Wi-Fi 6 pour une maison connectée sans coupure.', "Norme : Wi-Fi 6 (ax)\nDébit : jusqu'à 1800 Mbps\nPorts : 4x Gigabit\nCouverture : 120 m²\nGarantie : 12 mois"],
  ['electronique','Pixaro','Pixaro View 24 — Écran 24\"', 98000, 22, 'monitor.svg', 'Moniteur 24\" Full HD confortable pour le bureau.', "Dalle : 24\" IPS Full HD\nFréquence : 75Hz\nConnectique : HDMI, VGA\nInclinable : oui\nGarantie : 24 mois"],

  // ───── Accessoires ─────
  ['accessoires','Volt','Volt Power 20000 — Batterie externe', 15900, 120, 'powerbank.svg', 'Batterie externe grande capacité, charge rapide 2 ports.', "Capacité : 20000 mAh\nSortie : 22,5W\nPorts : 2x USB + USB-C\nIndicateur LED\nGarantie : 12 mois"],
  ['accessoires','Nexora','Nexora Fit — Montre connectée', 34900, 60, 'watch.svg', 'Montre connectée pour le sport et les notifications.', "Écran : 1,7\" AMOLED\nCapteurs : cardio, SpO2, pas\nÉtanchéité : 5 ATM\nAutonomie : 7 jours\nGarantie : 12 mois"],
  ['accessoires','Soniq','Soniq Beat — Casque sans fil', 24900, 80, 'headset.svg', 'Casque circum-auriculaire confortable, basses profondes.', "Bluetooth : 5.3\nRéduction de bruit\nAutonomie : 30h\nPliable\nGarantie : 12 mois"],
  ['accessoires','Volt','Volt Dash — Chargeur rapide 33W', 9900, 150, 'charger.svg', 'Chargeur secteur rapide compatible la plupart des téléphones.', "Puissance : 33W\nTechnologie : PD / QC\nEntrée : 100-240V\nCâble USB-C inclus\nGarantie : 12 mois"],
  ['accessoires','Volt','Volt Link — Clé USB 128 Go', 8900, 200, 'usbkey.svg', 'Clé USB 3.2 rapide et compacte pour vos fichiers.', "Capacité : 128 Go\nUSB 3.2\nVitesse : jusqu'à 120 Mo/s\nMatériau : métal\nGarantie : 24 mois"],
  ['accessoires','Nexora','Nexora Mouse — Souris sans fil', 6900, 140, 'mouse.svg', 'Souris sans fil silencieuse et ergonomique.', "Connexion : 2,4 GHz USB\nDPI : 800-1600\nSilencieuse\nPile : 1x AA (incluse)\nGarantie : 12 mois"],
  ['accessoires','Nexora','Nexora Keys — Clavier sans fil', 12900, 90, 'keyboard.svg', 'Clavier sans fil AZERTY compact et silencieux.', "Disposition : AZERTY\nConnexion : 2,4 GHz\nAutonomie : 6 mois\nSilencieux\nGarantie : 12 mois"],
  ['accessoires','Soniq','Soniq Buds — Écouteurs filaires', 4900, 220, 'earbuds.svg', 'Écouteurs filaires avec micro, son équilibré.', "Jack : 3,5 mm\nMicro : oui\nTélécommande\nLongueur : 1,2 m\nGarantie : 6 mois"],
  ['accessoires','Ivoria','Ivoria Case — Sacoche ordinateur 14\"', 11900, 70, 'handbag.svg', 'Sacoche rembourrée pour protéger votre ordinateur portable.', "Taille : jusqu'à 14\"\nMatière : polyester rembourré\nPoche avant\nBandoulière\nGarantie : 6 mois"],
  ['accessoires','Soniq','Soniq Hub — Enceinte Bluetooth', 27900, 65, 'speaker.svg', 'Enceinte portable puissante et résistante aux éclaboussures.', "Puissance : 20W\nBluetooth : 5.3\nAutonomie : 12h\nÉtanchéité : IPX6\nGarantie : 12 mois"],

  // ───── Maison ─────
  ['maison','Baobab','Baobab Breeze — Ventilateur sur pied', 29900, 35, 'fan.svg', 'Ventilateur sur pied silencieux avec télécommande.', "Vitesses : 3\nOscillation : oui\nHauteur réglable\nTélécommande\nGarantie : 12 mois"],
  ['maison','Baobab','Baobab Mix — Blender 1,5 L', 22900, 50, 'blender.svg', 'Blender puissant pour jus, soupes et sauces.', "Bol : 1,5 L verre\nMoteur : 500W\nLames inox\n2 vitesses + pulse\nGarantie : 24 mois"],
  ['maison','Baobab','Baobab Wave — Micro-ondes 20 L', 64900, 20, 'microwave.svg', 'Micro-ondes compact idéal pour réchauffer et décongeler.', "Capacité : 20 L\nPuissance : 700W\n6 niveaux\nMinuteur\nGarantie : 24 mois"],
  ['maison','Baobab','Baobab Press — Fer à repasser vapeur', 18900, 55, 'iron.svg', 'Fer vapeur avec semelle céramique anti-accroche.', "Puissance : 2200W\nSemelle : céramique\nVapeur : 25 g/min\nSpray + anti-calcaire\nGarantie : 12 mois"],
  ['maison','Baobab','Baobab Glow — Lampe de bureau LED', 9900, 80, 'lamp.svg', 'Lampe LED à intensité réglable pour le travail et la lecture.', "LED : 7W\n3 intensités\nCol flexible\nPort USB\nGarantie : 12 mois"],
  ['maison','Baobab','Baobab Hot — Bouilloire 1,7 L', 14900, 70, 'kettle.svg', 'Bouilloire rapide avec arrêt automatique.', "Capacité : 1,7 L\nPuissance : 2000W\nArrêt auto\nFiltre anti-calcaire\nGarantie : 24 mois"],
  ['maison','Baobab','Baobab Cool — Réfrigérateur compact', 189000, 12, 'fridge.svg', 'Réfrigérateur compact silencieux pour studio ou bureau.', "Volume : 90 L\nClasse : A+\nCompartiment congélation\nSilencieux\nGarantie : 24 mois"],
  ['maison','Baobab','Baobab Breeze Mini — Ventilateur de table', 12900, 90, 'fan.svg', 'Petit ventilateur de table, discret et efficace.', "Vitesses : 2\nOscillation : oui\nUSB\nSilencieux\nGarantie : 12 mois"],
  ['maison','Baobab','Baobab Toast — Grille-pain', 16900, 60, 'microwave.svg', 'Grille-pain 2 fentes avec 6 niveaux de brunissage.', "Fentes : 2 larges\n6 niveaux\nRamasse-miettes\nArrêt auto\nGarantie : 24 mois"],
  ['maison','Baobab','Baobab Blend Pro — Mixeur plongeant', 13900, 75, 'blender.svg', 'Mixeur plongeant pratique pour soupes et purées.', "Puissance : 600W\nPied inox\n2 vitesses\nBol doseur inclus\nGarantie : 24 mois"],

  // ───── Mode ─────
  ['mode','Waxéa','Waxéa — Pagne wax premium 6 yards', 15000, 100, 'wax.svg', 'Pagne wax de qualité, couleurs éclatantes, idéal couture.', "Longueur : 6 yards (5,5 m)\nMatière : 100% coton\nMotif : wax imprimé\nEntretien : lavage 30°C\nFabrication : Afrique de l'Ouest"],
  ['mode','Ivoria','Ivoria Street — Baskets urbaines', 27900, 70, 'shoe.svg', 'Baskets confortables et légères pour la ville.', "Dessus : textile + synthétique\nSemelle : caoutchouc\nPointures : 38-45\nAmorti : mousse EVA\nEntretien : brosse douce"],
  ['mode','Ivoria','Ivoria Robe Soleil — Robe coton', 19900, 60, 'dress.svg', 'Robe légère en coton, coupe fluide pour les journées chaudes.', "Matière : 100% coton\nTailles : S-XL\nCoupe : fluide\nEntretien : lavage 30°C\nColoris : assortis"],
  ['mode','Ivoria','Ivoria Tee — T-shirt coton bio', 7900, 150, 'tshirt.svg', 'T-shirt uni en coton bio, doux et respirant.', "Matière : 100% coton bio\nTailles : S-XXL\nCol rond\nEntretien : lavage 30°C\nColoris : assortis"],
  ['mode','Ivoria','Ivoria Denim — Jean slim', 21900, 80, 'jeans.svg', 'Jean coupe slim, denim stretch confortable.', "Matière : 98% coton, 2% élasthanne\nCoupe : slim\nTailles : 28-40\n5 poches\nEntretien : lavage à l'envers"],
  ['mode','Ivoria','Ivoria Lux — Sac à main', 32900, 40, 'handbag.svg', 'Sac à main élégant en simili-cuir, finition soignée.', "Matière : simili-cuir\nDimensions : 30x22x12 cm\nDoublure textile\nBandoulière amovible\nFermeture zip"],
  ['mode','Ivoria','Ivoria Belt — Ceinture cuir', 8900, 110, 'belt.svg', 'Ceinture en cuir véritable, boucle métal classique.', "Matière : cuir véritable\nLargeur : 3,5 cm\nBoucle : métal\nTailles : 90-115 cm\nColoris : noir / marron"],
  ['mode','Ivoria','Ivoria Breeze — Sandales', 11900, 95, 'sandal.svg', 'Sandales légères et confortables pour l\'été.', "Dessus : synthétique\nSemelle : caoutchouc\nPointures : 36-45\nRéglage : bride\nEntretien : chiffon humide"],
  ['mode','Waxéa','Waxéa Chemise — Chemise wax', 17900, 65, 'tshirt.svg', 'Chemise en tissu wax, coupe moderne, 100% coton.', "Matière : 100% coton wax\nTailles : S-XXL\nManches longues\nEntretien : lavage 30°C\nMotif : wax imprimé"],
  ['mode','Ivoria','Ivoria Run — Baskets running', 29900, 60, 'shoe.svg', 'Chaussures de running avec amorti réactif.', "Dessus : mesh respirant\nSemelle : EVA + caoutchouc\nPointures : 38-46\nDrop : 8 mm\nUsage : course / sport"],

  // ───── Beauté ─────
  ['beaute','Éclat','Éclat — Coffret soin visage', 18900, 45, 'beauty.svg', 'Coffret complet : nettoyant, crème et sérum aux actifs naturels.', "Contenu : 3 produits\nActifs : aloe vera, vitamine C\nType de peau : tous\nSans parabène\nFabrication : France"],
  ['beaute','Éclat','Éclat Parfum Nuit — Eau de parfum 50 ml', 24900, 50, 'perfume.svg', 'Eau de parfum boisée et chaude pour le soir.', "Volume : 50 ml\nFamille : boisé ambré\nTenue : 8h\nNotes : vanille, santal, ambre\nNon testé sur animaux"],
  ['beaute','Éclat','Éclat Crème Karité — Soin corps 200 ml', 6900, 120, 'cream.svg', 'Crème nourrissante au beurre de karité pour peaux sèches.', "Volume : 200 ml\nComposition : beurre de karité 20%, glycérine\nType : corps\nSans parfum de synthèse\nFabrication : Côte d'Ivoire"],
  ['beaute','Éclat','Éclat Rouge — Rouge à lèvres', 5900, 140, 'lipstick.svg', 'Rouge à lèvres longue tenue, fini mat confortable.', "Fini : mat\nTenue : 8h\nComposition : cires végétales, vitamine E\nColoris : assortis\nSans parabène"],
  ['beaute','Éclat','Éclat Shampoing Argan — 300 ml', 4900, 160, 'shampoo.svg', 'Shampoing nourrissant à l\'huile d\'argan pour cheveux secs.', "Volume : 300 ml\nActifs : huile d'argan, kératine\nType : cheveux secs\nSans sulfate\npH neutre"],
  ['beaute','Éclat','Éclat Savon Noir — Gommant 150 g', 3500, 180, 'soap.svg', 'Savon noir traditionnel gommant et purifiant.', "Poids : 150 g\nComposition : huile d'olive, potasse végétale\nUsage : gommage corps\n100% naturel\nFabrication artisanale"],
  ['beaute','Éclat','Éclat Sérum — Éclat visage 30 ml', 12900, 70, 'beauty.svg', 'Sérum concentré pour un teint lumineux et unifié.', "Volume : 30 ml\nActifs : vitamine C 10%, acide hyaluronique\nType : visage\nMatin et soir\nSans parabène"],
  ['beaute','Éclat','Éclat Huile Coco — Capillaire 250 ml', 4200, 150, 'oil.svg', 'Huile de coco pure pour cheveux et peau.', "Volume : 250 ml\nComposition : 100% huile de coco\nUsage : cheveux, peau\nPressée à froid\nNon raffinée"],
  ['beaute','Éclat','Éclat Parfum Jour — Eau de toilette 50 ml', 18900, 55, 'perfume.svg', 'Eau de toilette fraîche et florale pour la journée.', "Volume : 50 ml\nFamille : floral frais\nTenue : 5h\nNotes : agrumes, jasmin, musc\nNon testé sur animaux"],
  ['beaute','Éclat','Éclat Masque — Hydratant 100 ml', 7900, 90, 'cream.svg', 'Masque hydratant intense pour peaux déshydratées.', "Volume : 100 ml\nActifs : acide hyaluronique, aloe vera\nPose : 10 min\n2x par semaine\nSans parabène"],

  // ───── Alimentaire ─────
  ['alimentaire','Savane','Savane — Riz parfumé 25 kg', 21500, 200, 'rice.svg', 'Riz parfumé grain long, le sac familial pour le mois.', "Poids : 25 kg\nType : grain long parfumé\nOrigine : importé\nConservation : lieu sec\nDLUO : 18 mois"],
  ['alimentaire','Savane','Savane Huile — Huile végétale 5 L', 9900, 150, 'oil.svg', 'Huile végétale pour friture et cuisson du quotidien.', "Volume : 5 L\nType : huile végétale raffinée\nRiche en vitamine E\nConservation : à l'abri de la lumière\nDLUO : 12 mois"],
  ['alimentaire','Fresko','Fresko Lait — Lait en poudre 900 g', 6500, 180, 'milk.svg', 'Lait en poudre instantané, riche en calcium.', "Poids : 900 g\nType : lait entier en poudre\nEnrichi : calcium, vitamines A et D\nPréparation : eau tiède\nDLUO : 12 mois"],
  ['alimentaire','Fresko','Fresko Café — Café moulu 500 g', 5200, 160, 'coffee.svg', 'Café moulu arabica-robusta, arôme intense.', "Poids : 500 g\nComposition : arabica 60%, robusta 40%\nMouture : moyenne\nTorréfaction : intense\nDLUO : 18 mois"],
  ['alimentaire','Savane','Savane Sucre — Sucre blanc 1 kg', 1200, 300, 'sugar.svg', 'Sucre blanc cristallisé pour toutes vos préparations.', "Poids : 1 kg\nType : sucre blanc cristallisé\n100% canne\nConservation : lieu sec\nDLUO : 24 mois"],
  ['alimentaire','Savane','Savane Farine — Farine de blé 1 kg', 900, 280, 'sugar.svg', 'Farine de blé tout usage pour pâtisserie et cuisine.', "Poids : 1 kg\nType : farine de blé T55\nUsage : pain, pâtisserie\nConservation : lieu sec\nDLUO : 12 mois"],
  ['alimentaire','Fresko','Fresko Pâtes — Spaghetti 500 g', 1100, 260, 'pasta.svg', 'Pâtes de blé dur, cuisson rapide, tenue parfaite.', "Poids : 500 g\nType : spaghetti blé dur\nCuisson : 8-9 min\nSource de fibres\nDLUO : 24 mois"],
  ['alimentaire','Cacao d\'Or','Cacao d\'Or — Chocolat noir 100 g', 2500, 140, 'chocolate.svg', 'Tablette de chocolat noir ivoirien, 70% cacao.', "Poids : 100 g\nCacao : 70%\nOrigine : Côte d'Ivoire\nSans huile de palme\nDLUO : 12 mois"],
  ['alimentaire','Fresko','Fresko Jus — Jus d\'orange 1 L', 1800, 200, 'juice.svg', 'Jus d\'orange sans sucres ajoutés, vitaminé.', "Volume : 1 L\nComposition : jus d'orange 100%\nSans sucres ajoutés\nÀ conserver au frais après ouverture\nDLUO : 9 mois"],
  ['alimentaire','Fresko','Fresko Thé — Thé vert 50 sachets', 2200, 170, 'coffee.svg', 'Thé vert en sachets, léger et rafraîchissant.', "Quantité : 50 sachets\nType : thé vert\nInfusion : 3 min\nSans arôme artificiel\nDLUO : 24 mois"],
];
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
    [6,'awa@ics-market.local','Awa',5,'Les écouteurs sont top, le son est clair.'],
    [30,'fatou@ics-market.local','Fatou',5,'Très beau pagne, couleurs magnifiques.'],
    [50,'yao@ics-market.local','Yao',4,'Bon riz, le sac est costaud.'],
    [40,'ange@ics-market.local','Michael',5,'Le coffret soin est complet, peau douce.'],
    [11,'awa@ics-market.local','Awa',4,'Montre pratique, autonomie correcte.'],
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
