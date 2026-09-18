<?php
/**
 * DuitGW - Konfigurasi Aplikasi
 *
 * Nilai di bawah ini bisa di-override lewat environment variable
 * (dipakai saat deploy via Docker/Coolify) — kalau env var tidak
 * ada, fallback ke nilai default di sini (dipakai untuk shared
 * hosting / cPanel biasa tanpa Docker).
 */

function dw_env(string $key, string $default): string
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// ==== DATABASE ====
// Docker: DB_HOST otomatis diisi nama service (mis. "db") lewat docker-compose.yml
define('DB_HOST', dw_env('DB_HOST', 'srv-captain--mysql-db-db'));
define('DB_NAME', dw_env('DB_NAME', 'db_duitgw'));
define('DB_USER', dw_env('DB_USER', 'root'));
define('DB_PASS', dw_env('DB_PASS', 'Admin#1234'));
define('DB_CHARSET', 'utf8mb4');

// ==== APLIKASI ====
// BASE_URL wajib diisi tanpa trailing slash.
// Shared hosting (subfolder):  'https://mysite.com/duitgw/public'
// Docker + Cloudflare Tunnel:  'https://duitgw.namadomainmu.my.id'
define('BASE_URL', dw_env('BASE_URL', 'https://duitgw.ikhsanlab.my.id'));

define('APP_NAME', 'DuitGW');
define('APP_ENV', dw_env('APP_ENV', 'production')); // 'local' | 'production'

// ==== UPLOAD BUKTI FOTO ====
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/bukti');
define('UPLOAD_URL', BASE_URL . '/uploads/bukti');
define('UPLOAD_MAX_SIZE', 3 * 1024 * 1024); // 3MB
define('UPLOAD_ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp']);

// ==== SESSION ====
define('SESSION_NAME', 'duitgw_session');

// Tampilkan error hanya saat development
if (APP_ENV === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('Asia/Makassar');
