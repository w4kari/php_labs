<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/JokeValidator.php';

/**
 * Р­РєСЂР°РЅРёСЂСѓРµС‚ СЃС‚СЂРѕРєСѓ РґР»СЏ Р±РµР·РѕРїР°СЃРЅРѕРіРѕ РІС‹РІРѕРґР° РІ HTML.
 *
 * @param string $value РСЃС…РѕРґРЅР°СЏ СЃС‚СЂРѕРєР°.
 * @return string Р‘РµР·РѕРїР°СЃРЅР°СЏ СЃС‚СЂРѕРєР°.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Р’С‹РІРѕРґРёС‚ HTML-СЃС‚СЂР°РЅРёС†Сѓ СЃ СЃРѕРѕР±С‰РµРЅРёРµРј.
 *
 * @param string $title Р—Р°РіРѕР»РѕРІРѕРє СЃС‚СЂР°РЅРёС†С‹.
 * @param string $message РћСЃРЅРѕРІРЅРѕРµ СЃРѕРѕР±С‰РµРЅРёРµ.
 * @param array<int, string> $errors РЎРїРёСЃРѕРє РѕС€РёР±РѕРє.
 * @param bool $success РџСЂРёР·РЅР°Рє СѓСЃРїРµС€РЅРѕРіРѕ РІС‹РїРѕР»РЅРµРЅРёСЏ.
 * @return void
 */
function renderPage(string $title, string $message, array $errors = [], bool $success = false): void
{
    $statusClass = $success ? 'success' : 'error';
    $statusTitle = $success ? 'РЈСЃРїРµС€РЅРѕ' : 'РћС€РёР±РєР°';

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
    echo '                <p>Р РµР·СѓР»СЊС‚Р°С‚ РѕР±СЂР°Р±РѕС‚РєРё С„РѕСЂРјС‹ РєР°С‚Р°Р»РѕРіР° Р°РЅРµРєРґРѕС‚РѕРІ</p>';
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
        echo '                    <a href="index.php" class="btn btn-primary">Р”РѕР±Р°РІРёС‚СЊ РµС‰С‘ РѕРґРёРЅ Р°РЅРµРєРґРѕС‚</a>';
        echo '                    <a href="list_jokes.php" class="btn btn-secondary">РџРѕСЃРјРѕС‚СЂРµС‚СЊ РєР°С‚Р°Р»РѕРі</a>';
    } else {
        echo '                    <a href="javascript:history.back()" class="btn btn-primary">Р’РµСЂРЅСѓС‚СЊСЃСЏ РЅР°Р·Р°Рґ</a>';
        echo '                    <a href="index.php" class="btn btn-secondary">РџРµСЂРµР№С‚Рё Рє С„РѕСЂРјРµ</a>';
    }

    echo '                </div>';

    if ($success) {
        echo '            <div class="info-box">';
        echo '                Р—Р°РїРёСЃСЊ СѓСЃРїРµС€РЅРѕ СЃРѕС…СЂР°РЅРµРЅР° РІ С„Р°Р№Р» <strong>data.txt</strong>. РўРµРїРµСЂСЊ РµС‘ РјРѕР¶РЅРѕ СѓРІРёРґРµС‚СЊ РІ РѕР±С‰РµРј РєР°С‚Р°Р»РѕРіРµ.';
        echo '            </div>';
    } else {
        echo '            <div class="info-box">'; 
        echo '                РџСЂРѕРІРµСЂСЊС‚Рµ РІРІРµРґС‘РЅРЅС‹Рµ РґР°РЅРЅС‹Рµ Рё РїРѕРїСЂРѕР±СѓР№С‚Рµ РѕС‚РїСЂР°РІРёС‚СЊ С„РѕСЂРјСѓ РµС‰С‘ СЂР°Р·. Р”Р°, РґР°Р¶Рµ Р°РЅРµРєРґРѕС‚С‹ С‚СЂРµР±СѓСЋС‚ РґРёСЃС†РёРїР»РёРЅС‹.';
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
        'РќРµРІРµСЂРЅС‹Р№ Р·Р°РїСЂРѕСЃ',
        'Р”Р°РЅРЅС‹Рµ РґРѕР»Р¶РЅС‹ РѕС‚РїСЂР°РІР»СЏС‚СЊСЃСЏ С‚РѕР»СЊРєРѕ РјРµС‚РѕРґРѕРј POST.',
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
        'РћС€РёР±РєРё РІР°Р»РёРґР°С†РёРё',
        'Р¤РѕСЂРјР° СЃРѕРґРµСЂР¶РёС‚ РѕС€РёР±РєРё. РСЃРїСЂР°РІСЊС‚Рµ РёС… Рё РѕС‚РїСЂР°РІСЊС‚Рµ РґР°РЅРЅС‹Рµ Р·Р°РЅРѕРІРѕ.',
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
        'РћС€РёР±РєР° СЃРѕС…СЂР°РЅРµРЅРёСЏ',
        'РќРµ СѓРґР°Р»РѕСЃСЊ РїСЂРµРѕР±СЂР°Р·РѕРІР°С‚СЊ РґР°РЅРЅС‹Рµ РІ JSON.',
        [],
        false
    );
    exit;
}

$result = file_put_contents($file, $jsonLine . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($result === false) {
    renderPage(
        'РћС€РёР±РєР° СЃРѕС…СЂР°РЅРµРЅРёСЏ',
        'РќРµ СѓРґР°Р»РѕСЃСЊ СЃРѕС…СЂР°РЅРёС‚СЊ РґР°РЅРЅС‹Рµ РІ С„Р°Р№Р».',
        [],
        false
    );
    exit;
}

renderPage(
    'РђРЅРµРєРґРѕС‚ СѓСЃРїРµС€РЅРѕ СЃРѕС…СЂР°РЅС‘РЅ',
    'Р”Р°РЅРЅС‹Рµ Р±С‹Р»Рё СѓСЃРїРµС€РЅРѕ РѕР±СЂР°Р±РѕС‚Р°РЅС‹ Рё РґРѕР±Р°РІР»РµРЅС‹ РІ РєР°С‚Р°Р»РѕРі.',
    [],
    true
);
