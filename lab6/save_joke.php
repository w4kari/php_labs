<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/JokeValidator.php';

/**
 * Экранирует строку для безопасного вывода в HTML.
 *
 * @param string $value Исходная строка.
 * @return string Безопасная строка.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Выводит HTML-страницу с сообщением.
 *
 * @param string $title Заголовок страницы.
 * @param string $message Основное сообщение.
 * @param array<int, string> $errors Список ошибок.
 * @param bool $success Признак успешного выполнения.
 * @return void
 */
function renderPage(string $title, string $message, array $errors = [], bool $success = false): void
{
    $statusClass = $success ? 'success' : 'error';
    $statusTitle = $success ? 'Успешно' : 'Ошибка';

    echo '<!DOCTYPE html>';
    echo '<html lang="ru">';
    echo '<head>';
    echo '    <meta charset="UTF-8">';
    echo '    <meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '    <title>' . e($title) . '</title>';
    echo '    <style>
                * {
                    box-sizing: border-box;
                }

                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    background: linear-gradient(135deg, #eef2ff, #dbeafe);
                    color: #1f2937;
                }

                .page {
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 24px;
                }

                .card {
                    width: 100%;
                    max-width: 760px;
                    background: #ffffff;
                    border-radius: 22px;
                    overflow: hidden;
                    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.12);
                }

                .card-header {
                    padding: 28px 30px;
                    color: #fff;
                }

                .card-header.success {
                    background: linear-gradient(135deg, #16a34a, #15803d);
                }

                .card-header.error {
                    background: linear-gradient(135deg, #dc2626, #b91c1c);
                }

                .card-header h1 {
                    margin: 0 0 8px;
                    font-size: 30px;
                }

                .card-header p {
                    margin: 0;
                    font-size: 15px;
                    opacity: 0.95;
                }

                .card-body {
                    padding: 30px;
                }

                .message {
                    font-size: 17px;
                    line-height: 1.6;
                    margin-bottom: 20px;
                }

                .status-badge {
                    display: inline-block;
                    padding: 8px 14px;
                    border-radius: 999px;
                    font-size: 13px;
                    font-weight: bold;
                    margin-bottom: 18px;
                }

                .status-badge.success {
                    background: #dcfce7;
                    color: #166534;
                }

                .status-badge.error {
                    background: #fee2e2;
                    color: #991b1b;
                }

                .error-list {
                    margin: 0 0 24px;
                    padding: 0;
                    list-style: none;
                }

                .error-list li {
                    background: #fef2f2;
                    border: 1px solid #fecaca;
                    color: #991b1b;
                    padding: 12px 14px;
                    border-radius: 12px;
                    margin-bottom: 10px;
                    line-height: 1.5;
                }

                .actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 12px;
                    margin-top: 24px;
                }

                .btn {
                    display: inline-block;
                    text-decoration: none;
                    border: none;
                    border-radius: 12px;
                    padding: 14px 20px;
                    font-size: 15px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: 0.2s ease;
                }

                .btn-primary {
                    background: #5e5075;
                    color: white;
                }

                .btn-primary:hover {
                    background: #5e5075;
                    transform: translateY(-1px);
                }

                .btn-secondary {
                    background: #e5e7eb;
                    color: #111827;
                }

                .btn-secondary:hover {
                    background: #d1d5db;
                    transform: translateY(-1px);
                }

                .info-box {
                    margin-top: 22px;
                    padding: 16px;
                    border-radius: 14px;
                    background: #eff6ff;
                    border: 1px solid #bfdbfe;
                    color: #5e5075;
                    line-height: 1.6;
                    font-size: 14px;
                }

                @media (max-width: 640px) {
                    .card-header,
                    .card-body {
                        padding: 22px;
                    }

                    .card-header h1 {
                        font-size: 24px;
                    }

                    .actions {
                        flex-direction: column;
                    }

                    .btn {
                        width: 100%;
                        text-align: center;
                    }
                }
            </style>';
    echo '</head>';
    echo '<body>';
    echo '    <div class="page">';
    echo '        <div class="card">';
    echo '            <div class="card-header ' . e($statusClass) . '">';
    echo '                <h1>' . e($title) . '</h1>';
    echo '                <p>Результат обработки формы каталога анекдотов</p>';
    echo '            </div>';
    echo '            <div class="card-body">';
    echo '                <div class="status-badge ' . e($statusClass) . '">' . e($statusTitle) . '</div>';
    echo '                <div class="message">' . e($message) . '</div>';

    if (!empty($errors)) {
        echo '            <ul class="error-list">';
        foreach ($errors as $error) {
            echo '                <li>' . e($error) . '</li>';
        }
        echo '            </ul>';
    }

    echo '                <div class="actions">';

    if ($success) {
        echo '                    <a href="index.html" class="btn btn-primary">Добавить ещё один анекдот</a>';
        echo '                    <a href="list_jokes.php" class="btn btn-secondary">Посмотреть каталог</a>';
    } else {
        echo '                    <a href="javascript:history.back()" class="btn btn-primary">Вернуться назад</a>';
        echo '                    <a href="index.html" class="btn btn-secondary">Перейти к форме</a>';
    }

    echo '                </div>';

    if ($success) {
        echo '            <div class="info-box">';
        echo '                Запись успешно сохранена в файл <strong>data.txt</strong>. Теперь её можно увидеть в общем каталоге.';
        echo '            </div>';
    } else {
        echo '            <div class="info-box">';
        echo '                Проверьте введённые данные и попробуйте отправить форму ещё раз. Да, даже анекдоты требуют дисциплины.';
        echo '            </div>';
    }

    echo '            </div>';
    echo '        </div>';
    echo '    </div>';
    echo '</body>';
    echo '</html>';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderPage(
        'Неверный запрос',
        'Данные должны отправляться только методом POST.',
        [],
        false
    );
    exit;
}

$validator = new JokeValidator($_POST);

if (!$validator->validate()) {
    $allErrors = [];

    foreach ($validator->errors() as $fieldErrors) {
        foreach ($fieldErrors as $error) {
            $allErrors[] = $error;
        }
    }

    renderPage(
        'Ошибки валидации',
        'Форма содержит ошибки. Исправьте их и отправьте данные заново.',
        $allErrors,
        false
    );
    exit;
}

$joke = $validator->validated();

$file = 'data.txt';
$jsonLine = json_encode($joke, JSON_UNESCAPED_UNICODE);

if ($jsonLine === false) {
    renderPage(
        'Ошибка сохранения',
        'Не удалось преобразовать данные в JSON.',
        [],
        false
    );
    exit;
}

$result = file_put_contents($file, $jsonLine . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($result === false) {
    renderPage(
        'Ошибка сохранения',
        'Не удалось сохранить данные в файл.',
        [],
        false
    );
    exit;
}

renderPage(
    'Анекдот успешно сохранён',
    'Данные были успешно обработаны и добавлены в каталог.',
    [],
    true
);