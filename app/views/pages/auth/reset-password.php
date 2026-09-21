<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth/reset-password.css">
    <style>
        .success-box {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .cancel-link {
            text-align: center;
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="reset-wrap">
        <div class="reset-card">
            <a href="index.php?page=auth&action=login" class="back-link">
                <span class="back-arrow">←</span> Quay lại đăng nhập
            </a>

            <h2 class="reset-title">Khôi phục mật khẩu</h2>

            <?php if ($error): ?>
                <div class="form-error-box"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-box"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($step === 'request_email'): ?>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="request_email">

                    <div class="form-group">
                        <label for="email">Địa chỉ Email</label>
                        <div class="input-with-icon">
                            <span class="input-icon">✉️</span>
                            <input type="email" id="email" name="email" placeholder="Nhập email tài khoản của bạn" required>
                        </div>
                    </div>
                    <button type="submit" class="reset-btn">Gửi mã xác thực</button>
                </form>

            <?php elseif ($step === 'verify_otp'): ?>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="verify_otp">

                    <div class="form-group">
                        <label for="otp">Mã xác thực OTP</label>
                        <div class="input-with-icon">
                            <span class="input-icon">🔑</span>
                            <input type="text" id="otp" name="otp" placeholder="Nhập mã 6 số" required maxlength="6">
                        </div>
                        <small>Mã đã được gửi đến: <?= htmlspecialchars($_SESSION['reset_email']) ?></small>
                    </div>
                    <button type="submit" class="reset-btn">Xác thực</button>
                    <a href="index.php?index.php?page=auth&action=reset-password&cancel=1" class="cancel-link">Thử email khác</a>
                </form>

            <?php elseif ($step === 'new_password'): ?>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="new_password">

                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <div class="input-with-icon">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu mới" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số">
                            <button type="button" class="toggle-password" data-target="password" aria-label="Hiện mật khẩu">👁️</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Nhập lại mật khẩu</label>
                        <div class="input-with-icon">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số">
                            <button type="button" class="toggle-password" data-target="confirm_password" aria-label="Hiện mật khẩu">👁️</button>
                        </div>
                    </div>

                    <button type="submit" class="reset-btn">Lưu mật khẩu</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = document.getElementById(btn.dataset.target);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                btn.textContent = isHidden ? '🙈' : '👁️';
            });
        });
    </script>
</body>

</html>