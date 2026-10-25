<?php
/** Téléchargement de facture. */
require_login();
$file = $_GET['file'] ?? '';
$base = config()['storage_path'] . '/invoices/';
$path = $base . $file;

if ($file === '' || !is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Facture introuvable.";
    exit;
}
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: inline; filename="' . basename($file) . '"');
readfile($path);
