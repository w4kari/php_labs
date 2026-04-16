<?php
declare(strict_types=1);

require_once __DIR__ . '/../JokeValidator.php';
require_once __DIR__ . '/../ValidatorInterface.php';

const CATEGORIES = [
    'short' => 'Короткий',
    'family' => 'Семейный',
    'school' => 'Школьный',
    'work' => 'Про работу',
    'animals' => 'Про животных',
    'classic' => 'Классический',
];

const RATINGS = ['0+', '12+', '16+', '18+'];

/**
 * @param mixed $value
 */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * @param array<int, mixed>|string|mixed $tags
 */
function renderTags($tags): string
{
    if (is_array($tags)) {
        $normalized = [];
        foreach ($tags as $tag) {
            if (!is_string($tag)) {
                continue;
            }

            $trimmedTag = trim($tag);
            if ($trimmedTag !== '') {
                $normalized[] = $trimmedTag;
            }
        }

        return implode(', ', $normalized);
    }

    if (is_string($tags)) {
        return trim($tags);
    }

    return '';
}

function toggleOrder(string $currentOrder): string
{
    return strtolower($currentOrder) === 'asc' ? 'desc' : 'asc';
}


/**
 * @return array<int, array<string, mixed>>
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
 * @param array<int, array<string, mixed>> $jokes
 * @return array<int, array<string, mixed>>
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
        static function (array $a, array $b) use ($sortBy, $order): int {
            $valueA = $a[$sortBy] ?? '';
            $valueB = $b[$sortBy] ?? '';
            $result = strcmp((string)$valueA, (string)$valueB);
            return $order === 'desc' ? -$result : $result;
        }
    );

    return $jokes;
}

/**
 * @param array<string, mixed> $validatedJoke
 */
function appendJokeToFile(string $filename, array $validatedJoke): bool
{
    $jsonLine = json_encode($validatedJoke, JSON_UNESCAPED_UNICODE);
    if ($jsonLine === false) {
        return false;
    }

    $result = file_put_contents($filename, $jsonLine . PHP_EOL, FILE_APPEND | LOCK_EX);

    return $result !== false;
}
