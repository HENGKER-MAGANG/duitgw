<?php

session_start();

require __DIR__ . '/../config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($path)) {
        require $path;
    }
});

use App\Core\Router;
use App\Core\AuthMiddleware;
use App\Core\AdminMiddleware;
use App\Core\GuestMiddleware;
use App\Core\UserOnlyMiddleware;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TransactionController;
use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Api\StatsApiController;

$router = new Router();

// ---- Public / Guest ----
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout']);

// ---- User (role: user) ----
$router->get('/dashboard', [DashboardController::class, 'index'], [UserOnlyMiddleware::class]);
$router->get('/transactions', [TransactionController::class, 'index'], [UserOnlyMiddleware::class]);
$router->get('/transactions/create', [TransactionController::class, 'create'], [UserOnlyMiddleware::class]);
$router->post('/transactions', [TransactionController::class, 'store'], [UserOnlyMiddleware::class]);
$router->get('/transactions/{id}/edit', [TransactionController::class, 'edit'], [UserOnlyMiddleware::class]);
$router->post('/transactions/{id}', [TransactionController::class, 'update'], [UserOnlyMiddleware::class]);
$router->post('/transactions/{id}/delete', [TransactionController::class, 'destroy'], [UserOnlyMiddleware::class]);

// ---- Admin (role: admin, statistics view-only) ----
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/users/{id}', [AdminDashboardController::class, 'showUser'], [AdminMiddleware::class]);

// ---- JSON API (used by React islands) ----
$router->get('/api/stats/me', [StatsApiController::class, 'me'], [AuthMiddleware::class]);
$router->get('/api/stats/admin', [StatsApiController::class, 'admin'], [AdminMiddleware::class]);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
