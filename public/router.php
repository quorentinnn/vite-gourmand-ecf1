<?php
// Router pour le serveur PHP intégré (Railway)
// Sert les fichiers statiques et les pages admin/employe depuis le dossier parent

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dossiers statiques autorisés (en dehors de public/)
$staticDirs = ['CSS', 'JS', 'images', 'uploads'];

foreach ($staticDirs as $dir) {
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

// Pages PHP admin/ et employe/ (en dehors de public/)
$phpDirs = ['admin', 'employe'];

foreach ($phpDirs as $dir) {
    if (strpos($uri, '/' . $dir) === 0) {
        $file = dirname(__DIR__) . $uri;

        // /admin ou /admin/ → /admin/index.php
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/index.php';
        }

        if (file_exists($file) && is_file($file)) {
            chdir(dirname($file));
            include $file;
            return true;
        }

        http_response_code(404);
        echo "Page non trouvée";
        return true;
    }
}

// Laisser le serveur PHP gérer normalement (fichiers dans public/)
return false;
