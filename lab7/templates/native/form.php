<?php declare(strict_types=1); ?>
<?php if ($successMessage !== ''): ?>
    <div class="alert alert-success"><?= e($successMessage) ?></div>
<?php endif; ?>

<?php if (!empty($errors['_form'])): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors['_form'] as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="" method="POST" novalidate>
    <div class="form-grid">
        <div class="form-group">
            <label for="title">Название анекдота</label>
            <input type="text" id="title" name="title" required minlength="3" maxlength="100" value="<?= e($formData['title']) ?>" class="<?= !empty($errors['title']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['title'])): ?>
                <?php foreach ($errors['title'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="author">Автор / источник</label>
            <input type="text" id="author" name="author" required minlength="2" maxlength="100" value="<?= e($formData['author']) ?>" class="<?= !empty($errors['author']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['author'])): ?>
                <?php foreach ($errors['author'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group full-width">
            <label for="content">Текст анекдота</label>
            <textarea id="content" name="content" required minlength="10" maxlength="2000" class="<?= !empty($errors['content']) ? 'input-error' : '' ?>"><?= e($formData['content']) ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <?php foreach ($errors['content'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="category">Категория</label>
            <select id="category" name="category" required class="<?= !empty($errors['category']) ? 'input-error' : '' ?>">
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $formData['category'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['category'])): ?>
                <?php foreach ($errors['category'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="rating">Возрастной рейтинг</label>
            <select id="rating" name="rating" required class="<?= !empty($errors['rating']) ? 'input-error' : '' ?>">
                <option value="">Выберите рейтинг</option>
                <?php foreach ($ratings as $rating): ?>
                    <option value="<?= e($rating) ?>" <?= $formData['rating'] === $rating ? 'selected' : '' ?>><?= e($rating) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['rating'])): ?>
                <?php foreach ($errors['rating'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="publish_date">Дата публикации</label>
            <input type="date" id="publish_date" name="publish_date" required value="<?= e($formData['publish_date']) ?>" class="<?= !empty($errors['publish_date']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['publish_date'])): ?>
                <?php foreach ($errors['publish_date'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="created_at">Дата создания записи</label>
            <input type="date" id="created_at" name="created_at" required value="<?= e($formData['created_at']) ?>" class="<?= !empty($errors['created_at']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['created_at'])): ?>
                <?php foreach ($errors['created_at'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="updated_at">Дата обновления</label>
            <input type="date" id="updated_at" name="updated_at" required value="<?= e($formData['updated_at']) ?>" class="<?= !empty($errors['updated_at']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['updated_at'])): ?>
                <?php foreach ($errors['updated_at'] as $error): ?>
                    <div class="field-error"><?= e($error) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="tags">Теги</label>
            <?php foreach ($formData['tags'] as $index => $tag): ?>
                <input type="text" id="<?= $index === 0 ? 'tags' : 'tags_' . $index ?>" name="tags[]" maxlength="150" value="<?= e((string)$tag) ?>" style="<?= $index > 0 ? 'margin-top: 8px;' : '' ?>">
            <?php endforeach; ?>
            <div class="hint">Каждый тег передается отдельным элементом массива tags[].</div>
        </div>
    </div>

    <?php if (!empty($errors) && count($errors) > (isset($errors['_form']) ? 1 : 0)): ?>
        <div class="alert alert-error" style="margin-top: 20px;">Исправьте ошибки в полях формы и отправьте снова.</div>
    <?php endif; ?>

    <div class="actions" style="margin-top: 24px;">
        <button type="submit" class="btn btn-primary">Сохранить анекдот</button>
        <a href="index_twig.php" class="btn btn-primary">Добавить анекдот (Twig)</a>
        <a href="list_jokes.php" class="btn btn-secondary">Посмотреть каталог (PHP)</a>
        <a href="list_jokes_twig.php" class="btn btn-secondary">Посмотреть каталог (Twig)</a>
    </div>
</form>
