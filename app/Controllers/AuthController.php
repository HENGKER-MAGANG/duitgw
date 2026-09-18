<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'error' => $this->flash('error'),
            'success' => $this->flash('success'),
        ], null);
    }

    public function login(): void
    {
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->flash('error', 'Email atau kata sandi salah.');
            $this->redirect('login');
        }

        if ($user['status'] === 'suspended') {
            $this->flash('error', 'Akun kamu dinonaktifkan. Hubungi admin.');
            $this->redirect('login');
        }

        Auth::login($user);
        $this->redirect($user['role'] === 'admin' ? 'admin/dashboard' : 'dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [
            'error' => $this->flash('error'),
        ], null);
    }

    public function register(): void
    {
        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $passwordConfirm = $this->input('password_confirmation', '');

        $validator = new Validator(compact('name', 'email', 'password'));
        $validator->required('name', 'Nama')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('password', 'Kata sandi')
            ->min('password', 6, 'Kata sandi');

        if ($password !== $passwordConfirm) {
            $validator->addError('password_confirmation', 'Konfirmasi kata sandi tidak cocok.');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $validator->addError('email', 'Email sudah terdaftar.');
        }

        if ($validator->fails()) {
            $this->keepOld(compact('name', 'email'));
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('register');
        }

        $userId = $userModel->create($name, $email, $password, 'user');
        $user = $userModel->find($userId);
        Auth::login($user);
        $this->flash('success', 'Selamat datang di DuitGW, ' . $name . '!');
        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('login');
    }
}
