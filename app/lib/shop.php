<?php
/** ICS Market — fonctions e-commerce (réglages du site, panier). */
declare(strict_types=1);

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
