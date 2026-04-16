<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/src/handlers.php';
require_once __DIR__ . '/src/render_native.php';

$viewData = handleJokesListRequest($_GET, __DIR__ . '/data.txt');
$viewData['title'] = 'Список анекдотов';
$viewData['heading'] = 'Каталог анекдотов (Нативные PHP-шаблоны)';

renderNativeTemplate('list.php', $viewData);
