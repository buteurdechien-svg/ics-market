<?php
/** ICS Market — API REST /api/v1/. Utilisée par l'appli mobile web. */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

$pdo = Database::pdo();
$uri = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '', '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function body_json(): array { $j = json_decode(file_get_contents('php://input') ?: '[]', true); return is_array($j) ? $j : []; }
function json_out($d, int $c = 200): never { http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }

function api_user(): ?array {
    start_session();
    if (empty($_SESSION['uid'])) {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? (function_exists('apache_request_headers') ? (apache_request_headers()['Authorization'] ?? '') : '');
        if (preg_match('/Bearer\s+(\S+)/', (string)$hdr, $m)) {
            $st = Database::pdo()->prepare('SELECT * FROM users WHERE SHA1(CONCAT(id,"|",pass_hash)) = ?');
            $st->execute([$m[1]]); return $st->fetch() ?: null;
        }
        return null;
    }
    $st = Database::pdo()->prepare('SELECT * FROM users WHERE id = ?'); $st->execute([$_SESSION['uid']]);
    return $st->fetch() ?: null;
}

// POST /api/v1/login
if ($uri === '/api/v1/login' && $method === 'POST') {
    $b = body_json();
    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?'); $st->execute([strtolower(trim($b['email'] ?? ''))]);
    $u = $st->fetch();
    if (!$u || !password_verify($b['password'] ?? '', $u['pass_hash'])) json_out(['error'=>'invalid_credentials'], 401);
    $_SESSION['uid'] = (int)$u['id'];
    json_out(['token'=>sha1($u['id'].'|'.$u['pass_hash']), 'user_id'=>(int)$u['id'], 'role'=>$u['role']]);
}

// GET /api/v1/products
if ($uri === '/api/v1/products' && $method === 'GET') {
    json_out(['products'=>$pdo->query('SELECT id,name,price,stock,rating_avg FROM products WHERE status="active"')->fetchAll()]);
}

// GET /api/v1/orders/{id}
if (preg_match('#^/api/v1/orders/(\d+)$#', $uri, $m) && $method === 'GET') {
    $u = api_user(); if (!$u) json_out(['error'=>'unauthenticated'], 401);
    $st = $pdo->prepare('SELECT o.*, u.full_name, u.email FROM orders o LEFT JOIN users u ON u.id=o.user_id WHERE o.id = ?');
    $st->execute([(int)$m[1]]);
    $o = $st->fetch(); if (!$o) json_out(['error'=>'not_found'], 404);
    $it = $pdo->prepare('SELECT name,unit_price,qty FROM order_items WHERE order_id=?'); $it->execute([(int)$m[1]]);
    $o['items'] = $it->fetchAll();
    json_out($o);
}

// GET /api/v1/me
if ($uri === '/api/v1/me' && $method === 'GET') {
    $u = api_user(); if (!$u) json_out(['error'=>'unauthenticated'], 401);
    json_out(['id'=>(int)$u['id'],'full_name'=>$u['full_name'],'email'=>$u['email'],'role'=>$u['role'],'phone'=>$u['phone']]);
}

// PATCH /api/v1/profile
if ($uri === '/api/v1/profile' && in_array($method, ['PATCH','POST'], true)) {
    $u = api_user(); if (!$u) json_out(['error'=>'unauthenticated'], 401);
    $b = body_json();
    $cols = []; $vals = []; $echo = [];
    foreach ($b as $k => $v) {
        if (in_array($k, ['full_name','phone','email','role','status'], true)) { $cols[]="$k=?"; $vals[]=$v; $echo[$k]=$v; }
    }
    if ($cols) { $vals[]=$u['id']; $pdo->prepare('UPDATE users SET '.implode(',',$cols).' WHERE id=?')->execute($vals); }
    $st = $pdo->prepare('SELECT id,full_name,email,phone,role,status FROM users WHERE id=?'); $st->execute([$u['id']]);
    json_out(['updated'=>$echo, 'user'=>$st->fetch()]);
}

json_out(['error'=>'not_found','path'=>$uri], 404);
