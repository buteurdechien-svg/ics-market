<?php
/** ICS Market — point d'entrée unique (routeur). */
declare(strict_types=1);

require dirname(__DIR__) . '/app/lib/helpers.php';
require dirname(__DIR__) . '/app/lib/shop.php';
require dirname(__DIR__) . '/app/lib/Mailer.php';

start_session();
cart_load_cookie();

$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri    = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (str_starts_with($uri, '/api/')) {
    require dirname(__DIR__) . '/app/api.php';
    exit;
}

$ctl = dirname(__DIR__) . '/app/controllers/';

/* Routes avec paramètre */
if (preg_match('#^/c/([a-z0-9\-]+)$#', $uri, $m)) { $_GET['slug'] = $m[1]; require $ctl.'public/category.php'; exit; }
if (preg_match('#^/p/(\d+)#', $uri, $m))          { $_GET['id']   = $m[1]; require $ctl.'public/product.php'; exit; }
if (preg_match('#^/order/(\d+)$#', $uri, $m))     { $_GET['id']   = $m[1]; require $ctl.'account/order.php'; exit; }
if (preg_match('#^/invoice$#', $uri))             { require $ctl.'account/invoice.php'; exit; }
if (preg_match('#^/payment/receipt$#', $uri))     { require $ctl.'shop/receipt.php'; exit; }

$routes = [
    'GET /'           => 'public/home.php',
    'GET /search'     => 'public/search.php',
    'GET /debug'      => 'public/debug.php',

    'GET /cart'       => 'shop/cart.php',
    'POST /cart'      => 'shop/cart.php',
    'GET /checkout'   => 'shop/checkout.php',
    'POST /checkout'  => 'shop/checkout.php',
    'GET /payment'    => 'shop/payment.php',
    'POST /payment'   => 'shop/payment.php',

    'GET /login'      => 'auth/login.php',
    'POST /login'     => 'auth/login.php',
    'GET /register'   => 'auth/register.php',
    'POST /register'  => 'auth/register.php',
    'GET /verify'     => 'auth/verify.php',
    'POST /verify'    => 'auth/verify.php',
    'GET /forgot'     => 'auth/forgot.php',
    'POST /forgot'    => 'auth/forgot.php',
    'GET /logout'     => 'auth/logout.php',

    'GET /account'    => 'account/account.php',
    'GET /orders'     => 'account/orders.php',
    'GET /profile'    => 'account/profile.php',
    'POST /profile'   => 'account/profile.php',

    'GET /manage'     => 'pro/manage.php',
    'POST /manage'    => 'pro/manage.php',
];

$key = $method . ' ' . $uri;
if (isset($routes[$key])) { require $ctl . $routes[$key]; exit; }

http_response_code(404);
render('public/error', ['code'=>404,'message'=>'Page introuvable.','title'=>'Introuvable']);
