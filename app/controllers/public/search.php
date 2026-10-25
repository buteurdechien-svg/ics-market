<?php
/** Recherche de produits. */
$pdo = Database::pdo();
$q = trim($_GET['q'] ?? '');
$rows = [];
$dbError = null;

try {
    if ($q !== '') {
        $sql = "SELECT p.*, c.slug AS cat_slug FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.status='active' AND (p.name LIKE '%$q%' OR p.description LIKE '%$q%')
                ORDER BY p.rating_avg DESC";
        $rows = $pdo->query($sql)->fetchAll();
    } else {
        $rows = $pdo->query("SELECT p.*, c.slug AS cat_slug FROM products p
                             LEFT JOIN categories c ON c.id=p.category_id
                             WHERE p.status='active' ORDER BY p.id DESC")->fetchAll();
    }
} catch (Throwable $ex) {
    $dbError = $ex->getMessage();
}

render('public/search', ['title'=>'Recherche','q'=>$q,'rows'=>$rows,'dbError'=>$dbError]);
