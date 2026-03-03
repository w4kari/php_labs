<?php
declare(strict_types=1);

/**
 * Image gallery page.
 *
 * This script scans the "image/" directory for .jpg/.jpeg files and renders them
 * as an HTML gallery.
 *
 * Requirements:
 * - Directory "image/" must exist in the same folder as this script.
 * - Directory should contain 20-30 images with .jpg/.jpeg extensions.
 *
 * Output:
 * - HTML page with gallery layout and the number of found images.
 *
 * @author
 * @version 1.0
 */

/**
 * Absolute filesystem path to the images directory.
 *
 * Uses __DIR__ to ensure the path is based on the current script location.
 *
 * @var string
 */
$dir = __DIR__ . '/image/';

/**
 * Relative web path to the images directory.
 *
 * This path is used inside <img src="..."> and must match the URL structure.
 *
 * @var string
 */
$webDir = 'image/';

/**
 * List of all entries (files and folders) inside the images directory.
 *
 * scandir() returns an array including "." and ".." entries.
 *
 * @var array<int, string>|false
 */
$files = scandir($dir);

if ($files === false) {
    // If the directory cannot be scanned, stop the script with an error message.
    die('Не удалось прочитать папку image/');
}

/**
 * Filtered list of image file names with jpg/jpeg extensions.
 *
 * Only file names are stored here; final path is built using $webDir when rendering.
 *
 * @var array<int, string>
 */
$images = [];

// Collect only jpg/jpeg images
foreach ($files as $file) {
    // Skip current and parent directory entries
    if ($file === '.' || $file === '..') {
        continue;
    }

    // Extract file extension and compare case-insensitively
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if ($ext === 'jpg' || $ext === 'jpeg') {
        $images[] = $file;
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Галерея</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        header, footer { padding: 16px; background: #f2f2f2; }
        nav { padding: 10px 16px; background: #ddd; }
        nav a { margin-right: 12px; text-decoration: none; color: #000; }
        main { padding: 16px; }
        .gallery { display: flex; flex-wrap: wrap; gap: 12px; }
        .gallery img {
            width: 200px; height: 150px; object-fit: cover;
            border: 1px solid #ccc; border-radius: 6px;
        }
        .note { color: #555; }
    </style>
</head>
<body>

<header>
    <h1>Галерея манулов</h1>
</header>

<main>
    <h2 id="gallery">Изображения из папки image/</h2>

    <!-- Displays how many valid .jpg/.jpeg images were found -->
    <p class="note">Найдено файлов: <?= count($images) ?></p>

    <div class="gallery">
        <?php foreach ($images as $img): ?>
            <?php
            /**
             * Each image is rendered using a relative web path.
             * htmlspecialchars() is used for the alt text to prevent HTML injection.
             */
            ?>
            <img src="<?= $webDir . $img ?>" alt="<?= htmlspecialchars($img) ?>">
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>