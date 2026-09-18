<?php

namespace App\Core;

// Admin is view-only on stats; only 'user' role manages finances.
class UserOnlyMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . Router::url('login'));
            exit;
        }
        if (Auth::isAdmin()) {
            header('Location: ' . Router::url('admin/dashboard'));
            exit;
        }
    }
}
