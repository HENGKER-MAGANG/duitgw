<?php

namespace App\Core;

class AdminMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . Router::url('login'));
            exit;
        }
        if (!Auth::isAdmin()) {
            http_response_code(403);
            View::render('errors/403');
            exit;
        }
    }
}
