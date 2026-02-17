<?php
// Router pour le serveur PHP intégré
// Permet de servir les fichiers statiques depuis public/ et redirige les routes vers index.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$dir = dirname($path);

// Traiter les fichiers statiques (CSS, JS, images, etc.)
$file = __DIR__ . '/public' . $path;

// Vérifier si c'est un fichier existant dans public/
if (is_file($file) && strpos($path, '/public/') !== false || (strpos($path, '.css') !== false || strpos($path, '.js') !== false || strpos($path, '.png') !== false || strpos($path, '.jpg') !== false || strpos($path, '.gif') !== false || strpos($path, '.woff') !== false || strpos($path, '.woff2') !== false || strpos($path, '.ttf') !== false || strpos($path, '.ico') !== false)) {
    return false; // Le serveur servira le fichier
}

// Pour tout le reste, rediriger vers index.php
require __DIR__ . '/index.php';
