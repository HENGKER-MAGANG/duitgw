<?php

namespace App\Core;

class GuestMiddleware implements Middleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            $dest = Auth::isAdmin() ? 'admin/dashboard' : 'dashboard';
            header('Location: ' . Router::url($dest));
            exit;
        }
    }
}
