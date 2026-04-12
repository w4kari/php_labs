<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/JokeValidator.php';
require_once __DIR__ . '/ValidatorInterface.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$fields = [
    'title',
    'author',
    'content',
    'category',
    'rating',
    'publish_date',
    'created_at',
    'updated_at',
];

$formData = array_fill_keys($fields, '');
$formData['tags'] = ['', '', ''];
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($fields as $field) {
        $value = $_POST[$field] ?? '';
        $formData[$field] = is_string($value) ? trim($value) : '';
    }

    $rawTags = $_POST['tags'] ?? [];
    if (!is_array($rawTags)) {
        $rawTags = [$rawTags];
    }

    $preparedTags = [];
    foreach ($rawTags as $tag) {
        if (!is_string($tag)) {
            continue;
        }

        $trimmedTag = trim($tag);
        if ($trimmedTag !== '') {
            $preparedTags[] = $trimmedTag;
        }
    }

    while (count($preparedTags) < 3) {
        $preparedTags[] = '';
    }

    $formData['tags'] = $preparedTags;

    /** @var ValidatorInterface $validator Валидатор формы. */
    $validator = new JokeValidator($formData);

    if ($validator->validate()) {
        $joke = $validator->validated();
        $jsonLine = json_encode($joke, JSON_UNESCAPED_UNICODE);

        if ($jsonLine === false) {
            $errors['_form'][] = 'Не удалось преобразовать данные в JSON.';
        } else {
            $result = file_put_contents(__DIR__ . '/data.txt', $jsonLine . PHP_EOL, FILE_APPEND | LOCK_EX);
            if ($result === false) {
                $errors['_form'][] = 'Не удалось сохранить данные в файл.';
            } else {
                $successMessage = 'Анекдот успешно сохранен.';
                $formData = array_fill_keys($fields, '');
                $formData['tags'] = ['', '', ''];
            }
        }
    } else {
        $errors = $validator->errors();
    }
}

$categories = [
    'short' => 'Короткий',
    'family' => 'Семейный',
    'school' => 'Школьный',
    'work' => 'Про работу',
    'animals' => 'Про животных',
    'classic' => 'Классический',
];

$ratings = ['0+', '12+', '16+', '18+'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог анекдотов</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f3f4f6, #dbeafe);
            color: #1f2937;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .card {
            width: 100%;
            max-width: 850px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .card-header {
            background: #ab80c4;
            color: white;
            padding: 30px;
        }

        .card-header h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }

        .card-body {
            padding: 30px;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.45;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #111827;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            background: #f9fafb;
            transition: all 0.2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #5e5075;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(94, 80, 117, 0.2);
        }

        .input-error {
            border-color: #dc2626;
            background: #fff7f7;
        }

        .field-error {
            margin-top: 6px;
            font-size: 12px;
            color: #b91c1c;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .hint {
            margin-top: 6px;
            font-size: 12px;
            color: #6b7280;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            border: none;
            border-radius: 12px;
            padding: 14px 22px;
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
            background: #4e4063;
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

        .footer-note {
            margin-top: 25px;
            padding: 15px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            font-size: 14px;
            color: #5e5075;
        }

        @media (max-width: 768px) {
            .card-header h1 {
                font-size: 26px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .card-body,
            .card-header {
                padding: 22px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="card">
        <div class="card-header">
            <h1>Каталог анекдотов</h1>
        </div>

        <div class="card-body">
            <?php if ($successMessage !== ''): ?>
                <div class="alert alert-success"><?= e($successMessage) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors['_form'])): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors['_form'] as $error): ?>
                            <li><?= e((string)$error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="POST" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Название анекдота</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            required
                            minlength="3"
                            maxlength="100"
                            value="<?= e($formData['title']) ?>"
                            class="<?= !empty($errors['title']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['title'])): ?>
                            <?php foreach ($errors['title'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="author">Автор / источник</label>
                        <input
                            type="text"
                            id="author"
                            name="author"
                            required
                            minlength="2"
                            maxlength="100"
                            value="<?= e($formData['author']) ?>"
                            class="<?= !empty($errors['author']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['author'])): ?>
                            <?php foreach ($errors['author'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group full-width">
                        <label for="content">Текст анекдота</label>
                        <textarea
                            id="content"
                            name="content"
                            required
                            minlength="10"
                            maxlength="2000"
                            class="<?= !empty($errors['content']) ? 'input-error' : '' ?>"
                        ><?= e($formData['content']) ?></textarea>
                        <?php if (!empty($errors['content'])): ?>
                            <?php foreach ($errors['content'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="category">Категория</label>
                        <select
                            id="category"
                            name="category"
                            required
                            class="<?= !empty($errors['category']) ? 'input-error' : '' ?>"
                        >
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $formData['category'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['category'])): ?>
                            <?php foreach ($errors['category'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="rating">Возрастной рейтинг</label>
                        <select
                            id="rating"
                            name="rating"
                            required
                            class="<?= !empty($errors['rating']) ? 'input-error' : '' ?>"
                        >
                            <option value="">Выберите рейтинг</option>
                            <?php foreach ($ratings as $rating): ?>
                                <option value="<?= e($rating) ?>" <?= $formData['rating'] === $rating ? 'selected' : '' ?>><?= e($rating) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['rating'])): ?>
                            <?php foreach ($errors['rating'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="publish_date">Дата публикации</label>
                        <input
                            type="date"
                            id="publish_date"
                            name="publish_date"
                            required
                            value="<?= e($formData['publish_date']) ?>"
                            class="<?= !empty($errors['publish_date']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['publish_date'])): ?>
                            <?php foreach ($errors['publish_date'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="created_at">Дата создания записи</label>
                        <input
                            type="date"
                            id="created_at"
                            name="created_at"
                            required
                            value="<?= e($formData['created_at']) ?>"
                            class="<?= !empty($errors['created_at']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['created_at'])): ?>
                            <?php foreach ($errors['created_at'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="updated_at">Дата обновления</label>
                        <input
                            type="date"
                            id="updated_at"
                            name="updated_at"
                            required
                            value="<?= e($formData['updated_at']) ?>"
                            class="<?= !empty($errors['updated_at']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['updated_at'])): ?>
                            <?php foreach ($errors['updated_at'] as $error): ?>
                                <div class="field-error"><?= e((string)$error) ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="tags">Теги</label>
                        <?php foreach ($formData['tags'] as $index => $tag): ?>
                            <input
                                type="text"
                                id="<?= $index === 0 ? 'tags' : 'tags_' . $index ?>"
                                name="tags[]"
                                maxlength="150"
                                value="<?= e((string)$tag) ?>"
                                style="<?= $index > 0 ? 'margin-top: 8px;' : '' ?>"
                            >
                        <?php endforeach; ?>
                        <div class="hint">Каждый тег передается отдельным элементом массива `tags[]`.</div>
                    </div>
                </div>

                <?php if (!empty($errors) && count($errors) > (isset($errors['_form']) ? 1 : 0)): ?>
                    <div class="alert alert-error" style="margin-top: 20px;">
                        Исправьте ошибки в полях формы и отправьте снова.
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Сохранить анекдот</button>
                    <a href="list_jokes.php" class="btn btn-secondary">Посмотреть каталог</a>
                </div>

                <div class="footer-note">
                    Все поля, кроме тегов, обязательны для заполнения.
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
