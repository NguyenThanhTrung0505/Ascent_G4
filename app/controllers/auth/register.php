<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/auth/register.php';

$error = null;
$success = null;
$step = $_SESSION['step'] ?? 'register';

if (isset($_GET['cancel'])) {
    unset($_SESSION['step']);
    $step = 'register';
    header("Location: index.php?page=auth&action=register");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'register') {
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $role     = trim($_POST['role'] ?? 'student');
        if ($fullname === '' || $email === '' || $password === '' || $confirm === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin.';
        } elseif ($password !== $confirm) {
            $error = 'Mật khẩu nhập lại không khớp.';
        } else {
            $userExist = findUserByEmail($email);
            if ($userExist) {
                $error = 'Email này đã được đăng ký.';
            } else {
                $otp = rand(100000, 999999);
                $_SESSION['temp_user'] = [
                    'fullname' => $fullname,
                    'email'    => $email,
                    'password' => $password,
                    'role'     => $role,
                    'otp'      => $otp,
                    'expires'  => time() + 300
                ];
                $subject = "Mã OTP đăng ký tài khoản";
                $message = "Xin chào $fullname,\n\nMã OTP xác nhận đăng ký của bạn là: $otp\nMã này có hiệu lực trong 5 phút.";
                $headers = "From: noreply@yourdomain.com";
                @mail($email, $subject, $message, $headers);
                echo "<script>alert('DEV MODE - Mã OTP của bạn là: {$otp}');</script>";
                $_SESSION['step'] = 'verify_otp';
                $step = 'verify_otp';
            }
        }
    } elseif ($action === 'verify_otp') {
        $user_otp = trim($_POST['otp'] ?? '');

        if (!isset($_SESSION['temp_user'])) {
            $error = 'Phiên đăng ký không hợp lệ hoặc đã hủy.';
            $step = 'register';
        } elseif (time() > $_SESSION['temp_user']['expires']) {
            $error = 'Mã OTP đã hết hạn (quá 5 phút). Vui lòng đăng ký lại.';
            unset($_SESSION['temp_user'], $_SESSION['step']);
            $step = 'register';
        } elseif ($user_otp !== (string)$_SESSION['temp_user']['otp']) {
            $error = 'Mã OTP không chính xác.';
        } else {
            $userData = $_SESSION['temp_user'];
            $hashed_password = password_hash($userData['password'], PASSWORD_DEFAULT);

            $isCreated = createUser(
                $userData['fullname'],
                $userData['email'],
                $hashed_password,
                $userData['role']
            );

            if ($isCreated) {
                $success = 'Đăng ký thành công! Hãy đăng nhập.';
                unset($_SESSION['temp_user'], $_SESSION['step']);
                $step = 'success';
                header("Location: index.php?page=auth&action=login");
                exit;
            } else {
                $error = 'Có lỗi xảy ra khi tạo tài khoản vào CSDL.';
            }
        }
    }
}

require_once ROOT_PATH . '/app/views/pages/auth/register.php';
