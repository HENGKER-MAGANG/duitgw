<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    public function forUser(int $userId, ?string $type = null): array
    {
        $sql = "SELECT * FROM categories WHERE user_id = ?";
        $params = [$userId];
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function firstOrCreate(int $userId, string $name, string $type): int
    {
        $stmt = $this->db->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ? AND type = ? LIMIT 1");
        $stmt->execute([$userId, $name, $type]);
        $row = $stmt->fetch();
        if ($row) {
            return (int) $row['id'];
        }
        return $this->insert(['user_id' => $userId, 'name' => $name, 'type' => $type]);
    }
}
