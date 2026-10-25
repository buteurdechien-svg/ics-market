<?php
/** Page catégorie. */
$pdo = Database::pdo();
$slug = $_GET['slug'] ?? '';
$cat = $pdo->prepare('SELECT * FROM categories WHERE slug = ?'); $cat->execute([$slug]);
$category = $cat->fetch();
if (!$category) { http_response_code(404); render('public/error',['code'=>404,'message'=>'Catégorie introuvable.','title'=>'Introuvable']); exit; }

$ps = $pdo->prepare('SELECT * FROM products WHERE category_id = ? AND status="active" ORDER BY id DESC');
$ps->execute([$category['id']]);
render('public/category', ['title'=>$category['name'],'nav'=>$slug,'category'=>$category,'products'=>$ps->fetchAll()]);
