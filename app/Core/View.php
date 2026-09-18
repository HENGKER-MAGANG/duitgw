<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View tidak ditemukan: {$view}";
            exit;
        }

        if ($layout === null) {
            require $viewPath;
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../Views/' . $layout . '.php';
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function rupiah($amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }
}
