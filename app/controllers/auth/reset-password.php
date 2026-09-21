<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/models/auth/reset-password.php';

$error = null;
$success = null;

$step = $_SESSION['reset_step'] ?? 'request_email';

if (isset($_GET['cancel'])) {
    unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_expires'], $_SESSION['reset_step']);
    header("Location: index.php?page=reset-password");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'request_email') {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $error = 'Vui lòng nhập địa chỉ email.';
        } else {
            $userExist = findUserByEmail($email);
            if (!$userExist) {
                $error = 'Không tìm thấy tài khoản nào với email này.';
            } else {
                $otp = rand(100000, 999999);

                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_otp'] = $otp;
                $_SESSION['reset_expires'] = time() + 300;
                $subject = "Mã OTP khôi phục mật khẩu";
                $message = "Mã OTP khôi phục mật khẩu của bạn là: $otp\nMã này có hiệu lực trong 5 phút.";
                $headers = "From: noreply@yourdomain.com";
                @mail($email, $subject, $message, $headers);
                echo "<script>alert('DEV MODE - Mã OTP của bạn là: {$otp}');</script>";
                $_SESSION['reset_step'] = 'verify_otp';
                $step = 'verify_otp';
            }
        }
    } elseif ($action === 'verify_otp') {
        $user_otp = trim($_POST['otp'] ?? '');

        if (!isset($_SESSION['reset_email'])) {
            $error = 'Phiên làm việc không hợp lệ.';
            $step = 'request_email';
        } elseif (time() > $_SESSION['reset_expires']) {
            $error = 'Mã OTP đã hết hạn. Vui lòng thử lại.';
            unset($_SESSION['reset_step']);
            $step = 'request_email';
        } elseif ($user_otp !== (string)$_SESSION['reset_otp']) {
            $error = 'Mã OTP không chính xác.';
        } else {
            $_SESSION['reset_step'] = 'new_password';
            $step = 'new_password';
        }
    } elseif ($action === 'new_password') {
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($password === '' || $confirm === '') {
            $error = 'Vui lòng nhập đầy đủ mật khẩu.';
        } elseif (strlen($password) < 6) {
            $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
        } elseif ($password !== $confirm) {
            $error = 'Mật khẩu nhập lại không khớp.';
        } else {
            $email = $_SESSION['reset_email'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            if (updateUserPassword($email, $hashed_password)) {
                $success = 'Đổi mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.';
                unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_expires'], $_SESSION['reset_step']);
                $step = 'success';
            } else {
                $error = 'Có lỗi xảy ra, không thể cập nhật mật khẩu.';
            }
        }
    }
}

require_once ROOT_PATH . '/app/views/pages/auth/reset-password.php';
