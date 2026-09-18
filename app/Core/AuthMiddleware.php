<?php

namespace App\Core;

class AuthMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . Router::url('login'));
            exit;
        }
    }
}
