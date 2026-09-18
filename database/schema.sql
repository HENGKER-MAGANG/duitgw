-- =========================================================
-- DuitGW - Skema Database
-- Import file ini melalui phpMyAdmin / mysql CLI sebelum
-- menjalankan aplikasi.
-- =========================================================

CREATE DATABASE IF NOT EXISTS `duitgw` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `duitgw`;

-- ---------------------------------------------------------
-- Tabel users
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `avatar_color` VARCHAR(7) NOT NULL DEFAULT '#C1121F',
    `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel categories (kategori transaksi, dibuat per user)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(60) NOT NULL,
    `type` ENUM('masuk', 'keluar') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel transactions
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NULL,
    `type` ENUM('masuk', 'keluar') NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `proof_photo` VARCHAR(255) NOT NULL COMMENT 'Nama file bukti foto (wajib)',
    `transaction_date` DATE NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX idx_user_date (`user_id`, `transaction_date`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Seed: akun admin default
-- Email   : admin@duitgw.test
-- Password: admin123
-- (hash dibuat dengan password_hash('admin123', PASSWORD_DEFAULT))
-- GANTI PASSWORD INI SEGERA SETELAH LOGIN PERTAMA KALI.
-- ---------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `role`, `avatar_color`)
VALUES (
    'Admin DuitGW',
    'admin@duitgw.test',
    '$2b$10$yVzTvrtFRxe/6H8XFd/cjugAnIbgk3H2VauYEwfq6uCFFAvbHpeZG',
    'admin',
    '#C1121F'
)
ON DUPLICATE KEY UPDATE `email` = `email`;

-- ---------------------------------------------------------
-- Seed: kategori default untuk contoh (opsional, aman dihapus)
-- ---------------------------------------------------------
