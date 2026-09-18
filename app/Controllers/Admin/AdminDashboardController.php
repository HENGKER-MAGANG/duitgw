<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\Transaction;

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        $userModel = new User();
        $transactionModel = new Transaction();

        $users = $userModel->allWithStats();

        $totalMasuk = array_sum(array_column($users, 'total_masuk'));
        $totalKeluar = array_sum(array_column($users, 'total_keluar'));

        $this->view('admin/dashboard', [
            'users' => $users,
            'totalUsers' => count($users),
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'totalTransaksi' => array_sum(array_column($users, 'total_transaksi')),
        ]);
    }

    public function showUser(string $id): void
    {
        $userModel = new User();
        $user = $userModel->find((int) $id);

        if (!$user || $user['role'] !== 'user') {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $transactionModel = new Transaction();

        $this->view('admin/users/show', [
            'targetUser' => $user,
            'summary' => $transactionModel->summaryForUser((int) $id),
        ]);
    }
}
