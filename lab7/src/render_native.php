<?php
declare(strict_types=1);

/**
 * @param array<string, mixed> $params
 */
function renderNativeTemplate(string $view, array $params = []): void
{
    $templatesPath = __DIR__ . '/../templates/native';
    $viewPath = $templatesPath . '/' . $view;

    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo 'Template not found: ' . e($view);
        return;
    }

    extract($params, EXTR_SKIP);

    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    require $templatesPath . '/layout.php';
}
