<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Upload;
use App\Core\Validator;
use App\Models\Transaction;
use App\Models\Category;

class TransactionController extends Controller
{
    private function wantsJson(): bool
    {
        return ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json'
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function index(): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();

        $filters = [
            'type' => $this->input('type'),
            'month' => $this->input('month'),
            'search' => $this->input('search'),
        ];

        $transactions = $transactionModel->forUser($userId, array_filter($filters));

        $this->view('user/transactions/index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'success' => $this->flash('success'),
        ]);
    }

    public function create(): void
    {
        $categoryModel = new Category();
        $this->view('user/transactions/create', [
            'categories' => $categoryModel->forUser(Auth::id()),
            'error' => $this->flash('error'),
        ]);
    }

    public function store(): void
    {
        $userId = Auth::id();

        $data = [
            'type' => $this->input('type'),
            'amount' => $this->input('amount'),
            'description' => trim((string) $this->input('description', '')),
            'transaction_date' => $this->input('transaction_date'),
            'category_name' => trim((string) $this->input('category_name', '')),
        ];

        $validator = new Validator($data);
        $validator->required('type', 'Jenis transaksi')
            ->required('amount', 'Jumlah')
            ->numeric('amount', 'Jumlah')
            ->min_value('amount', 0, 'Jumlah')
            ->required('description', 'Keterangan')
            ->required('transaction_date', 'Tanggal');

        if (!in_array($data['type'], ['masuk', 'keluar'], true)) {
            $validator->addError('type', 'Jenis transaksi tidak valid.');
        }

        $proofFilename = null;
        try {
            $proofFilename = Upload::bukti($_FILES['proof_photo'] ?? [], $userId);
        } catch (\RuntimeException $e) {
            $validator->addError('proof_photo', $e->getMessage());
        }

        if ($validator->fails()) {
            if ($proofFilename) {
                Upload::delete($proofFilename);
            }
            $message = implode(' ', $validator->errors());
            if ($this->wantsJson()) {
                $this->json(['success' => false, 'errors' => $validator->errors(), 'message' => $message], 422);
            }
            $this->flash('error', $message);
            $this->redirect('transactions/create');
        }

        $categoryId = null;
        if (!empty($data['category_name'])) {
            $categoryModel = new Category();
            $categoryId = $categoryModel->firstOrCreate($userId, $data['category_name'], $data['type']);
        }

        $transactionModel = new Transaction();
        $id = $transactionModel->insert([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'proof_photo' => $proofFilename,
            'transaction_date' => $data['transaction_date'],
        ]);

        if ($this->wantsJson()) {
            $this->json(['success' => true, 'id' => $id, 'redirect' => \App\Core\Router::url('transactions')]);
        }

        $this->flash('success', 'Transaksi berhasil dicatat.');
        $this->redirect('transactions');
    }

    public function edit(string $id): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();
        $transaction = $transactionModel->findForUser((int) $id, $userId);

        if (!$transaction) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $categoryModel = new Category();
        $this->view('user/transactions/edit', [
            'transaction' => $transaction,
            'categories' => $categoryModel->forUser($userId),
            'error' => $this->flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();
        $transaction = $transactionModel->findForUser((int) $id, $userId);

        if (!$transaction) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }

        $data = [
            'type' => $this->input('type'),
            'amount' => $this->input('amount'),
            'description' => trim((string) $this->input('description', '')),
            'transaction_date' => $this->input('transaction_date'),
        ];

        $validator = new Validator($data);
        $validator->required('type', 'Jenis transaksi')
            ->required('amount', 'Jumlah')
            ->numeric('amount', 'Jumlah')
            ->min_value('amount', 0, 'Jumlah')
            ->required('description', 'Keterangan')
            ->required('transaction_date', 'Tanggal');

        $newProof = null;
        if (!empty($_FILES['proof_photo']['name'])) {
            try {
                $newProof = Upload::bukti($_FILES['proof_photo'], $userId);
            } catch (\RuntimeException $e) {
                $validator->addError('proof_photo', $e->getMessage());
            }
        }

        if ($validator->fails()) {
            if ($newProof) {
                Upload::delete($newProof);
            }
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('transactions/' . $id . '/edit');
        }

        $updateData = [
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'transaction_date' => $data['transaction_date'],
        ];

        if ($newProof) {
            $updateData['proof_photo'] = $newProof;
            Upload::delete($transaction['proof_photo']);
        }

        $transactionModel->update((int) $id, $updateData);

        $this->flash('success', 'Transaksi berhasil diperbarui.');
        $this->redirect('transactions');
    }

    public function destroy(string $id): void
    {
        $userId = Auth::id();
        $transactionModel = new Transaction();
        $transaction = $transactionModel->findForUser((int) $id, $userId);

        if ($transaction) {
            Upload::delete($transaction['proof_photo']);
            $transactionModel->delete((int) $id);
            $this->flash('success', 'Transaksi berhasil dihapus.');
        }

        $this->redirect('transactions');
    }
}
