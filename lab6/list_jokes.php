<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

/**
 * Читает записи анекдотов из файла JSON Lines.
 *
 * @param string $filename Путь к файлу с данными.
 * @return array<int, array<string, string>> Массив записей.
 */
function readJokesFromFile(string $filename): array
{
    if (!file_exists($filename)) {
        return [];
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return [];
    }

    $jokes = [];

    foreach ($lines as $line) {
        $decoded = json_decode($line, true);

        if (is_array($decoded)) {
            $jokes[] = $decoded;
        }
    }

    return $jokes;
}

/**
 * Сортирует массив анекдотов по указанному полю.
 *
 * @param array<int, array<string, string>> $jokes Массив записей.
 * @param string $sortBy Поле сортировки.
 * @param string $order Направление сортировки: asc или desc.
 * @return array<int, array<string, string>> Отсортированный массив.
 */
function sortJokes(array $jokes, string $sortBy, string $order): array
{
    $allowedFields = ['title', 'category', 'author', 'publish_date', 'created_at', 'updated_at', 'rating'];

    if (!in_array($sortBy, $allowedFields, true)) {
        $sortBy = 'created_at';
    }

    $order = strtolower($order) === 'desc' ? 'desc' : 'asc';

    usort(
        $jokes,
        function (array $a, array $b) use ($sortBy, $order): int {
            $valueA = $a[$sortBy] ?? '';
            $valueB = $b[$sortBy] ?? '';

            $result = strcmp((string)$valueA, (string)$valueB);

            return $order === 'desc' ? -$result : $result;
        }
    );

    return $jokes;
}

/**
 * Экранирует строку для безопасного вывода в HTML.
 *
 * @param string|null $value Исходное значение.
 * @return string Безопасная строка.
 */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$sortBy = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'asc';

$jokes = readJokesFromFile('data.txt');
$jokes = sortJokes($jokes, $sortBy, $order);

/**
 * Возвращает противоположное направление сортировки.
 *
 * @param string $currentOrder Текущее направление.
 * @return string Новое направление.
 */
function toggleOrder(string $currentOrder): string
{
    return strtolower($currentOrder) === 'asc' ? 'desc' : 'asc';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список анекдотов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            color: #222;
        }

        .actions {
            margin-bottom: 20px;
        }

        .actions a {
            text-decoration: none;
            color: white;
            background: #5e5075;
            padding: 10px 14px;
            border-radius: 6px;
            margin-right: 10px;
            display: inline-block;
        }

        .actions a:hover {
            background: #5e5075;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            border: 1px solid #dcdcdc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
        }

        th a {
            color: #222;
            text-decoration: none;
        }

        th a:hover {
            text-decoration: underline;
        }

        tr:nth-child(even) {
            background: #fafafa;
        }

        .content-cell {
            max-width: 350px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .empty {
            padding: 20px;
            background: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 8px;
            color: #664d03;
        }

        .sort-info {
            margin-bottom: 15px;
            color: #555;
        }

        @media (max-width: 900px) {
            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Каталог анекдотов</h1>

    <div class="actions">
        <a href="index.html">Добавить анекдот</a>
    </div>

    <p class="sort-info">
        Текущая сортировка:
        <strong><?= e($sortBy) ?></strong>,
        порядок:
        <strong><?= e($order) ?></strong>
    </p>

    <?php if (empty($jokes)): ?>
        <div class="empty">
            Пока нет сохранённых анекдотов.
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th><a href="?sort=title&order=<?= e(toggleOrder($order)) ?>">Название</a></th>
                <th><a href="?sort=category&order=<?= e(toggleOrder($order)) ?>">Категория</a></th>
                <th>Текст анекдота</th>
                <th><a href="?sort=author&order=<?= e(toggleOrder($order)) ?>">Автор</a></th>
                <th><a href="?sort=publish_date&order=<?= e(toggleOrder($order)) ?>">Дата публикации</a></th>
                <th><a href="?sort=created_at&order=<?= e(toggleOrder($order)) ?>">Дата создания</a></th>
                <th><a href="?sort=updated_at&order=<?= e(toggleOrder($order)) ?>">Дата обновления</a></th>
                <th><a href="?sort=rating&order=<?= e(toggleOrder($order)) ?>">Рейтинг</a></th>
                <th>Теги</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($jokes as $joke): ?>
                <tr>
                    <td><?= e($joke['title'] ?? '') ?></td>
                    <td><?= e($joke['category'] ?? '') ?></td>
                    <td class="content-cell"><?= e($joke['content'] ?? '') ?></td>
                    <td><?= e($joke['author'] ?? '') ?></td>
                    <td><?= e($joke['publish_date'] ?? '') ?></td>
                    <td><?= e($joke['created_at'] ?? '') ?></td>
                    <td><?= e($joke['updated_at'] ?? '') ?></td>
                    <td><?= e($joke['rating'] ?? '') ?></td>
                    <td><?= e($joke['tags'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>