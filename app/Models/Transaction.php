<?php

namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function forUser(int $userId, array $filters = []): array
    {
        $sql = "
            SELECT t.*, c.name AS category_name
            FROM transactions t
            LEFT JOIN categories c ON c.id = t.category_id
            WHERE t.user_id = :user_id
        ";
        $params = ['user_id' => $userId];

        if (!empty($filters['type'])) {
            $sql .= " AND t.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['month'])) {
            $sql .= " AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month";
            $params['month'] = $filters['month'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND t.description LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findForUser(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function summaryForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END), 0) AS total_masuk,
                COALESCE(SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END), 0) AS total_keluar,
                COUNT(*) AS total_transaksi
            FROM transactions WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $row['saldo'] = ((float) $row['total_masuk']) - ((float) $row['total_keluar']);
        return $row;
    }

    /** Monthly trend (last 6 months) for a single user, used by the React chart. */
    public function monthlyTrend(int $userId, int $months = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(transaction_date, '%Y-%m') AS bulan,
                COALESCE(SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END), 0) AS masuk,
                COALESCE(SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END), 0) AS keluar
            FROM transactions
            WHERE user_id = :user_id
                AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY bulan
            ORDER BY bulan ASC
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':months', $months, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Category breakdown for a single user (for the 'keluar' pie/bar). */
    public function categoryBreakdown(int $userId, string $type = 'keluar'): array
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(c.name, 'Tanpa Kategori') AS kategori,
                SUM(t.amount) AS total
            FROM transactions t
            LEFT JOIN categories c ON c.id = t.category_id
            WHERE t.user_id = ? AND t.type = ?
            GROUP BY kategori
            ORDER BY total DESC
            LIMIT 8
        ");
        $stmt->execute([$userId, $type]);
        return $stmt->fetchAll();
    }

    /** Platform-wide monthly trend across all users, for the admin dashboard chart. */
    public function monthlyTrendAllUsers(int $months = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT
                DATE_FORMAT(transaction_date, '%Y-%m') AS bulan,
                COALESCE(SUM(CASE WHEN type = 'masuk' THEN amount ELSE 0 END), 0) AS masuk,
                COALESCE(SUM(CASE WHEN type = 'keluar' THEN amount ELSE 0 END), 0) AS keluar
            FROM transactions
            WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY bulan
            ORDER BY bulan ASC
        ");
        $stmt->bindValue(':months', $months, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
