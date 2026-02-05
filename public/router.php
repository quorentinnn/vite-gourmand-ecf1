<?php
// Router pour le serveur PHP intégré (Railway)
// Sert les fichiers statiques (CSS, JS, images) depuis le dossier parent

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dossiers statiques autorisés (en dehors de public/)
$allowedDirs = ['CSS', 'JS', 'images'];

foreach ($allowedDirs as $dir) {
    if (strpos($uri, '/' . $dir . '/') === 0) {
        $file = dirname(__DIR__) . $uri;
        if (file_exists($file) && is_file($file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mimeTypes = [
                'css'  => 'text/css',
                'js'   => 'application/javascript',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
                'svg'  => 'image/svg+xml',
                'ico'  => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2'=> 'font/woff2',
            ];
            $contentType = $mimeTypes[$ext] ?? mime_content_type($file);
            header('Content-Type: ' . $contentType);
            readfile($file);
            return true;
        }
    }
}

// Laisser le serveur PHP gérer normalement (fichiers dans public/)
return false;
