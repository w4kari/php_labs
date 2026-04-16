<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/twig.php';

$viewData = handleJokesListRequest($_GET, __DIR__ . '/data.txt');
$viewData['nextOrder'] = toggleOrder($viewData['order']);
$twig = createTwigEnvironment();

echo $twig->render('list.twig', $viewData);
