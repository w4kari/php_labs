<?php
declare(strict_types=1);

require_once __DIR__ . '/ValidatorInterface.php';

/**
 * Валидатор формы добавления анекдота.
 */
class JokeValidator implements ValidatorInterface
{
    /**
     * Исходные данные формы.
     *
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * Ошибки валидации.
     *
     * @var array<string, array<int, string>>
     */
    private array $errors = [];

    /**
     * Очищенные данные.
     *
     * @var array<string, mixed>
     */
    private array $validated = [];

    /**
     * Допустимые категории.
     *
     * @var array<int, string>
     */
    private array $allowedCategories = ['short', 'family', 'school', 'work', 'animals', 'classic'];

    /**
     * Допустимые возрастные рейтинги.
     *
     * @var array<int, string>
     */
    private array $allowedRatings = ['0+', '12+', '16+', '18+'];

    /**
     * @param array<string, mixed> $data Данные формы.
     */
    public function __construct(array $data)
    {
        $this->data = $this->sanitize($data);
    }

    /**
     * Очищает входные данные.
     *
     * @param array<string, mixed> $data Исходные данные.
     * @return array<string, mixed> Очищенные данные.
     */
    private function sanitize(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            $clean[$key] = is_string($value) ? trim($value) : $value;
        }

        return $clean;
    }

    /**
     * Проверяет корректность даты в формате YYYY-MM-DD.
     *
     * @param string $date Проверяемая дата.
     * @return bool Результат проверки.
     */
    private function isValidDate(string $date): bool
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);

        return $dateTime !== false && $dateTime->format('Y-m-d') === $date;
    }

    /**
     * Добавляет ошибку по полю.
     *
     * @param string $field Имя поля.
     * @param string $message Текст ошибки.
     * @return void
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Запускает валидацию формы.
     *
     * @return bool True, если ошибок нет.
     */
    public function validate(): bool
    {
        $title = (string)($this->data['title'] ?? '');
        $content = (string)($this->data['content'] ?? '');
        $category = (string)($this->data['category'] ?? '');
        $author = (string)($this->data['author'] ?? '');
        $publishDate = (string)($this->data['publish_date'] ?? '');
        $createdAt = (string)($this->data['created_at'] ?? '');
        $updatedAt = (string)($this->data['updated_at'] ?? '');
        $rating = (string)($this->data['rating'] ?? '');
        $tags = (string)($this->data['tags'] ?? '');

        if ($title === '') {
            $this->addError('title', 'Название обязательно для заполнения.');
        } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 100) {
            $this->addError('title', 'Название должно содержать от 3 до 100 символов.');
        }

        if ($content === '') {
            $this->addError('content', 'Текст анекдота обязателен.');
        } elseif (mb_strlen($content) < 10 || mb_strlen($content) > 2000) {
            $this->addError('content', 'Текст анекдота должен содержать от 10 до 2000 символов.');
        }

        if ($category === '') {
            $this->addError('category', 'Категория обязательна.');
        } elseif (!in_array($category, $this->allowedCategories, true)) {
            $this->addError('category', 'Недопустимая категория.');
        }

        if ($author === '') {
            $this->addError('author', 'Автор обязателен.');
        }

        if ($publishDate === '' || !$this->isValidDate($publishDate)) {
            $this->addError('publish_date', 'Дата публикации указана неверно.');
        }

        if ($createdAt === '' || !$this->isValidDate($createdAt)) {
            $this->addError('created_at', 'Дата создания указана неверно.');
        }

        if ($updatedAt === '' || !$this->isValidDate($updatedAt)) {
            $this->addError('updated_at', 'Дата обновления указана неверно.');
        }

        if ($rating === '') {
            $this->addError('rating', 'Возрастной рейтинг обязателен.');
        } elseif (!in_array($rating, $this->allowedRatings, true)) {
            $this->addError('rating', 'Недопустимый возрастной рейтинг.');
        }

        if (empty($this->errors)) {
            $this->validated = [
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'author' => $author,
                'publish_date' => $publishDate,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'rating' => $rating,
                'tags' => $tags,
            ];
        }

        return empty($this->errors);
    }

    /**
     * Возвращает ошибки валидации.
     *
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает очищенные данные после успешной валидации.
     *
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->validated;
    }
}