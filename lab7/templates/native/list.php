<?php declare(strict_types=1); ?>
<div class="actions">
    <a href="index.php" class="btn btn-primary">Добавить анекдот (PHP)</a>
    <a href="index_twig.php" class="btn btn-primary">Добавить анекдот (Twig)</a>
</div>

<p class="sort-info">
    Текущая сортировка: <strong><?= e($sortBy) ?></strong>, порядок: <strong><?= e($order) ?></strong>
</p>

<?php if (empty($jokes)): ?>
    <div class="empty">Пока нет сохраненных анекдотов.</div>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th><a href="?sort=title&order=<?= e(toggleOrder($order)) ?>">Название</a></th>
            <th><a href="?sort=category&order=<?= e(toggleOrder($order)) ?>">Категория</a></th>
            <th>Текст</th>
            <th><a href="?sort=author&order=<?= e(toggleOrder($order)) ?>">Автор</a></th>
            <th><a href="?sort=publish_date&order=<?= e(toggleOrder($order)) ?>">Публикация</a></th>
            <th><a href="?sort=created_at&order=<?= e(toggleOrder($order)) ?>">Создание</a></th>
            <th><a href="?sort=updated_at&order=<?= e(toggleOrder($order)) ?>">Обновление</a></th>
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
                <td><?= e(renderTags($joke['tags'] ?? [])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
