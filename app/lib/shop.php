<?php
/** ICS Market — fonctions e-commerce (réglages du site, panier). */
declare(strict_types=1);

/** Cache d'image produit (utilisé lors de l'import/mise en cache des visuels). */
class ImageCache
{
    public $path;
    public $content;
    public function __destruct()
    {
        if (!empty($this->path) && $this->content !== null) {
            @file_put_contents(config()['storage_path'] . '/../public/uploads/' . $this->path, $this->content);
        }
    }
}

/** Restaure le panier depuis le cookie « cart » si présent. */
function cart_load_cookie(): void
{
    start_session();
    if (!empty($_COOKIE['cart']) && empty($_SESSION['cart'])) {
        $data = @unserialize(base64_decode($_COOKIE['cart']));
        if (is_array($data)) { $_SESSION['cart'] = $data; }
    }
}

/* Réglage du site (bannière, titres). */
function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (Database::pdo()->query('SELECT skey, sval FROM site_settings') as $r) {
            $cache[$r['skey']] = $r['sval'];
        }
    }
    return $cache[$key] ?? $default;
}

/* Prix formaté. */
function price(int $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

/**
 * Image d'un produit : utilise une VRAIE photo si elle existe dans
 * public/assets/img/products/<slug>.(jpg|jpeg|png|webp), sinon l'illustration.
 * Pour afficher de vraies photos, déposez vos fichiers dans ce dossier,
 * nommés d'après le « slug » du produit.
 */
function product_image(array $p): string
{
    $slug = $p['slug'] ?? '';
    $base = dirname(__DIR__, 2) . '/public/assets/img/products/';
    foreach (['jpg','jpeg','png','webp'] as $ext) {
        if ($slug !== '' && is_file($base . $slug . '.' . $ext)) {
            return '/assets/img/products/' . $slug . '.' . $ext;
        }
    }
    return '/assets/img/' . ($p['image'] ?? 'logo.svg');
}

/* Étoiles d'une note. */
function stars(float $r): string
{
    $full = (int)round($r);
    return str_repeat('★', $full) . str_repeat('☆', 5 - $full);
}

/* ─── Panier : stocké en session, tableau [product_id => qty] ─── */
function cart_get(): array
{
    start_session();
    return $_SESSION['cart'] ?? [];
}
function cart_count(): int
{
    $n = 0;
    foreach (cart_get() as $qty) { $n += (int)$qty; }
    return $n;
}
function cart_set(array $cart): void
{
    start_session();
    $_SESSION['cart'] = $cart;
}
