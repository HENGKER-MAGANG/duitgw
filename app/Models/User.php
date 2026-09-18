<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $name, string $email, string $password, string $role = 'user'): int
    {
        $colors = ['#C1121F', '#780000', '#E8A33D', '#2D6A4F', '#003049'];
        return $this->insert([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'avatar_color' => $colors[array_rand($colors)],
        ]);
    }

    /**
     * All regular users with aggregated transaction stats.
     * Used by admin to view read-only statistics per user.
     */
    public function allWithStats(): array
    {
        $sql = "
            SELECT
                u.id, u.name, u.email, u.avatar_color, u.status, u.created_at,
                COALESCE(SUM(CASE WHEN t.type = 'masuk' THEN t.amount ELSE 0 END), 0) AS total_masuk,
                COALESCE(SUM(CASE WHEN t.type = 'keluar' THEN t.amount ELSE 0 END), 0) AS total_keluar,
                COUNT(t.id) AS total_transaksi,
                MAX(t.transaction_date) AS transaksi_terakhir
            FROM users u
            LEFT JOIN transactions t ON t.user_id = u.id
            WHERE u.role = 'user'
            GROUP BY u.id
            ORDER BY u.name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function countUsers(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) c FROM users WHERE role = 'user'")->fetch()['c'];
    }
}
