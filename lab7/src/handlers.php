<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/**
 * @return array{formData: array<string, mixed>, errors: array<string, array<int, string>>, successMessage: string, categories: array<string, string>, ratings: array<int, string>}
 */
function handleJokeFormRequest(string $requestMethod, array $post, string $dataFilePath): array
{
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

    if ($requestMethod === 'POST') {
        foreach ($fields as $field) {
            $value = $post[$field] ?? '';
            $formData[$field] = is_string($value) ? trim($value) : '';
        }

        $rawTags = $post['tags'] ?? [];
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

        $validator = new JokeValidator($formData);

        if ($validator->validate()) {
            if (appendJokeToFile($dataFilePath, $validator->validated())) {
                $successMessage = 'Анекдот успешно сохранен.';
                $formData = array_fill_keys($fields, '');
                $formData['tags'] = ['', '', ''];
            } else {
                $errors['_form'][] = 'Не удалось сохранить данные в файл.';
            }
        } else {
            $errors = $validator->errors();
        }
    }

    return [
        'formData' => $formData,
        'errors' => $errors,
        'successMessage' => $successMessage,
        'categories' => CATEGORIES,
        'ratings' => RATINGS,
    ];
}

/**
 * @return array{jokes: array<int, array<string, mixed>>, sortBy: string, order: string}
 */
function handleJokesListRequest(array $query, string $dataFilePath): array
{
    $sortBy = isset($query['sort']) && is_string($query['sort']) ? $query['sort'] : 'created_at';
    $order = isset($query['order']) && is_string($query['order']) ? $query['order'] : 'asc';

    $jokes = readJokesFromFile($dataFilePath);
    $jokes = sortJokes($jokes, $sortBy, $order);

    return [
        'jokes' => $jokes,
        'sortBy' => $sortBy,
        'order' => $order,
    ];
}
