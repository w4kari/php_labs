<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

function createTwigEnvironment(): Environment
{
    $autoloadFile = __DIR__ . '/../vendor/autoload.php';

    if (!file_exists($autoloadFile)) {
        http_response_code(500);
        echo 'Twig не установлен. Выполните: composer install';
        exit;
    }

    require_once $autoloadFile;

    $loader = new FilesystemLoader(__DIR__ . '/../templates/twig');
    $twig = new Environment($loader, [
        'cache' => false,
        'strict_variables' => false,
        'autoescape' => 'html',
    ]);

    $twig->addFilter(
        new TwigFilter(
            'human_date',
            static function (string $date): string {
                $dateTime = DateTime::createFromFormat('Y-m-d', $date);
                if ($dateTime === false || $dateTime->format('Y-m-d') !== $date) {
                    return $date;
                }

                $today = new DateTimeImmutable('today');
                $updated = DateTimeImmutable::createFromFormat('Y-m-d', $date);
                if ($updated === false) {
                    return $date;
                }

                $days = (int)$updated->diff($today)->format('%a');
                $label = $days === 1 ? 'день' : 'дней';

                return $date . ' (' . $days . ' ' . $label . ' назад)';
            }
        )
    );

    return $twig;
}
