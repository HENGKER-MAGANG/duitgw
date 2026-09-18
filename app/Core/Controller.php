<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . Router::url($path));
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function flash(string $key, ?string $message = null)
    {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }

        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    protected function old(string $key, $default = '')
    {
        $value = $_SESSION['old'][$key] ?? $default;
        unset($_SESSION['old'][$key]);
        return $value;
    }

    protected function keepOld(array $data): void
    {
        $_SESSION['old'] = $data;
    }
}
