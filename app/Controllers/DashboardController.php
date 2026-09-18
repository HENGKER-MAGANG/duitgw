<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index(): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();

        $summary = $transactionModel->summaryForUser($userId);
        $recent = $transactionModel->forUser($userId);
        $recent = array_slice($recent, 0, 6);

        $this->view('user/dashboard', [
            'summary' => $summary,
            'recent' => $recent,
            'success' => $this->flash('success'),
        ]);
    }
}
