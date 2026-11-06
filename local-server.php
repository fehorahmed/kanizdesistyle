<?php
// Local development router for this backup's root entry and public/build assets.
$root = __DIR__;
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$asset = null;
if (str_starts_with($path, '/build/')) {
    $asset = realpath($root . '/public' . $path);
    $allowed = realpath($root . '/public/build');
} elseif (preg_match('#^/(themes|img|fonts|uploads)/#', $path)) {
    $asset = realpath($root . $path);
    $allowed = realpath($root . '/' . explode('/', trim($path, '/'))[0]);
}
if ($asset && isset($allowed) && $allowed && str_starts_with($asset, $allowed . '/') && is_file($asset) && preg_match('/\.(css|js|json|png|jpe?g|gif|webp|svg|ico|woff2?|ttf|otf|mp4|webm|pdf)$/i', $asset)) {
    $ext = strtolower(pathinfo($asset, PATHINFO_EXTENSION));
    $types = ['css'=>'text/css', 'js'=>'application/javascript', 'json'=>'application/json', 'svg'=>'image/svg+xml', 'woff'=>'font/woff', 'woff2'=>'font/woff2'];
    header('Content-Type: ' . ($types[$ext] ?? mime_content_type($asset)));
    readfile($asset);
    return;
}
if (preg_match('#(^|/)\.|\.(env|php|sql|log|zip|lock|md)$#i', $path) || str_starts_with($path, '/vendor/')) {
    http_response_code(404);
    exit('Not found');
}
require $root . '/index.php';
