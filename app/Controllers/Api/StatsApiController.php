<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Transaction;
use App\Models\User;

class StatsApiController extends Controller
{
    /** GET /api/stats/me — for the logged-in user's own dashboard chart. */
    public function me(): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();

        $this->json([
            'summary' => $transactionModel->summaryForUser($userId),
            'trend' => $transactionModel->monthlyTrend($userId),
            'categories' => $transactionModel->categoryBreakdown($userId, 'keluar'),
        ]);
    }

    /** GET /api/stats/admin — aggregate + per-user stats, admin-only (read-only). */
    public function admin(): void
    {
        if (!Auth::isAdmin()) {
            $this->json(['message' => 'Forbidden'], 403);
        }

        $userModel = new User();
        $transactionModel = new Transaction();

        $users = $userModel->allWithStats();
        foreach ($users as &$u) {
            $u['saldo'] = (float) $u['total_masuk'] - (float) $u['total_keluar'];
            $u['total_masuk'] = (float) $u['total_masuk'];
            $u['total_keluar'] = (float) $u['total_keluar'];
            $u['total_transaksi'] = (int) $u['total_transaksi'];
        }
        unset($u);

        $this->json([
            'users' => $users,
            'trend' => $transactionModel->monthlyTrendAllUsers(),
        ]);
    }
}
