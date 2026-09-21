<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth/register.css">
    <style>
        .role-select {
            width: 100%;
            padding: 10px 10px 10px 35px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Nunito', sans-serif;
        }

        .success-box {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="register-wrap">
        <div class="register-inner">
            <div class="register-logo"><img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt="" width="84" height="84"></div>

            <?php if ($step === 'register'): ?>
                <h2 class="register-title">Chào mừng các bạn!</h2>
                <p class="register-sub">Tạo tài khoản để học tập</p>
            <?php elseif ($step === 'verify_otp'): ?>
                <h2 class="register-title">Xác thực Email</h2>
                <p class="register-sub">Vui lòng nhập mã OTP đã gửi tới email</p>
            <?php endif; ?>

            <div class="register-card">
                <?php if ($error): ?>
                    <div class="form-error-box"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-box"><?= htmlspecialchars($success) ?></div>
                    <p class="login-row"><a href="index.php?page=login">Bấm vào đây để đăng nhập</a></p>
                <?php endif; ?>
                <?php
                $old_fullname = $_SESSION['temp_user']['fullname'] ?? '';
                $old_email    = $_SESSION['temp_user']['email'] ?? '';
                $old_role     = $_SESSION['temp_user']['role'] ?? 'student';
                ?>
                <?php if ($step === 'register' && !$success): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="register">

                        <div class="form-group">
                            <label for="fullname">Họ và tên</label>
                            <div class="input-with-icon">
                                <span class="input-icon">👤</span>
                                <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên của bạn" required minlength="3"
                                    pattern="[a-zA-ZÀ-ỹ\s]+"
                                    title="Tên chỉ được chứa chữ cái và khoảng trắng"
                                    value="<?= htmlspecialchars($old_fullname) ?>">
                            </div>
                        </div>

                        <div class=" form-group">
                            <label for="email">Địa chỉ email</label>
                            <div class="input-with-icon">
                                <span class="input-icon">✉️</span>
                                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required value="<?= htmlspecialchars($old_email) ?>">
                            </div>
                        </div>

                        <div class=" form-group">
                            <label for="role">Bạn là?</label>
                            <div class="input-with-icon">
                                <span class="input-icon">🎓</span>
                                <select id="role" name="role" class="role-select" required>
                                    <option value="student" <?= $old_role === 'student' ? 'selected' : '' ?>>Học sinh</option>
                                    <option value="teacher" <?= $old_role === 'teacher' ? 'selected' : '' ?>>Giáo viên</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <div class="input-with-icon">
                                <span class="input-icon">🔒</span>
                                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
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

                        <button type="submit" class="submit-btn">Tiếp tục 🚀</button>

                        <p class="login-row">Đã có tài khoản? <a href="index.php?page=auth&action=login">Đăng nhập</a></p>
                    </form>

                <?php elseif ($step === 'verify_otp' && !$success): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="verify_otp">

                        <div class="form-group">
                            <label for="otp">Mã OTP (6 chữ số)</label>
                            <div class="input-with-icon">
                                <span class="input-icon">🔑</span>
                                <input type="text" id="otp" name="otp" placeholder="Nhập mã OTP" required maxlength="6">
                            </div>
                            <small>Mã đã được gửi đến: <?= htmlspecialchars($_SESSION['temp_user']['email']) ?></small>
                        </div>

                        <button type="submit" class="submit-btn">Xác thực & Đăng ký ✔️</button>

                        <p class="login-row" style="margin-top:15px;">
                            <a href="index.php?page=auth&action=register&cancel=1">Quay lại sửa thông tin</a>
                        </p>
                    </form>
                <?php endif; ?>

            </div>
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
        const registerForm = document.querySelector('form[action=""]');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        if (registerForm && passwordInput && confirmPasswordInput) {
            registerForm.addEventListener('submit', function(e) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    e.preventDefault();
                    alert("Mật khẩu nhập lại không khớp! Vui lòng kiểm tra lại.");
                }
            });
        }
    </script>
</body>

</html>