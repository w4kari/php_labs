<?php
declare(strict_types=1);

require_once __DIR__ . '/ValidatorInterface.php';

if (!function_exists('mb_strlen')) {
    /**
     * Fallback for environments where mbstring is not loaded.
     */
    function mb_strlen(string $string, ?string $encoding = null): int
    {
        if ($string === '') {
            return 0;
        }

        $result = preg_match_all('/./u', $string);

        if ($result === false) {
            return strlen($string);
        }

        return $result;
    }
}

/**
 * Валидатор формы добавления анекдота.
 */
class JokeValidator implements ValidatorInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @var array<string, array<int, string>>
     */
    private array $errors = [];

    /**
     * @var array<string, mixed>
     */
    private array $validated = [];

    /**
     * @var array<int, string>
     */
    private array $allowedCategories = ['short', 'family', 'school', 'work', 'animals', 'classic'];

    /**
     * @var array<int, string>
     */
    private array $allowedRatings = ['0+', '12+', '16+', '18+'];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $this->sanitize($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function sanitize(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            $clean[$key] = is_string($value) ? trim($value) : $value;
        }

        return $clean;
    }

    private function isValidDate(string $date): bool
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime !== false && $dateTime->format('Y-m-d') === $date;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

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
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->validated;
    }
}
