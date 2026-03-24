<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ─────────────────────────────────────────────────────────────
// Hébergement mutualisé : le serveur web pointe vers la racine
// du projet au lieu de public/. On redirige les fichiers
// statiques vers public/ et on corrige DOCUMENT_ROOT.
// ─────────────────────────────────────────────────────────────
$publicPath = __DIR__ . '/public';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    return false;
}

$_SERVER['DOCUMENT_ROOT'] = $publicPath;

if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/vendor/autoload.php';

(require_once __DIR__ . '/bootstrap/app.php')
    ->handleRequest(Request::capture());
