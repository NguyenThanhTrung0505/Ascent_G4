<?php

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/UserModel.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['username'];
        $redirect = match ($user['role']) {
            'teacher' => 'index.php?page=teacher&action=dashboard',
            'admin'   => 'index.php?page=admin&action=dashboard',
            default   => 'index.php?page=student&action=dashboard',
        };

        header("Location: {$redirect}");
        exit;
    } else {
        $error = 'Email hoặc mật khẩu không đúng.';
    }
}

require_once ROOT_PATH . '/app/views/pages/auth/login.php';
