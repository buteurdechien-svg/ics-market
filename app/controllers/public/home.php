<?php
/** Accueil ICS Market. */
$pdo = Database::pdo();
$featured = $pdo->query(
    'SELECT p.*, c.slug AS cat_slug FROM products p LEFT JOIN categories c ON c.id=p.category_id
     WHERE p.status="active" ORDER BY p.rating_avg DESC, p.id LIMIT 8'
)->fetchAll();
$newest = $pdo->query(
    'SELECT p.*, c.slug AS cat_slug FROM products p LEFT JOIN categories c ON c.id=p.category_id
     WHERE p.status="active" ORDER BY p.id DESC LIMIT 4'
)->fetchAll();
render('public/home', ['title'=>'Accueil','featured'=>$featured,'newest'=>$newest]);
