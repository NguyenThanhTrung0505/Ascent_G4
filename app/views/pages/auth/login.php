<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth/login.css">
</head>

<body>

    <div class="login-wrap">
        <div class="login-hero">
            <span class="hero-circle circle-1"></span>
            <span class="hero-circle circle-2"></span>
            <span class="hero-circle circle-3"></span>

            <div class="hero-icons">
                <div class="hero-icon icon-smile">🙂</div>
                <div class="hero-icon icon-people">👥</div>
                <div class="hero-icon icon-book">📖</div>
            </div>

            <h1 class="hero-title">Học Vui, Thi Thoải Mái!</h1>
            <p class="hero-sub">Nơi kiến thức gặp gỡ niềm vui</p>

            <div class="hero-features">
                <div class="feature-badge">
                    <span class="feature-emoji">📚</span>
                    <span>Học sinh hào hứng</span>
                </div>
                <div class="feature-badge">
                    <span class="feature-emoji">✨</span>
                    <span>Giáo viên dễ dàng</span>
                </div>
                <div class="feature-badge">
                    <span class="feature-emoji">🎯</span>
                    <span>Kết quả rõ ràng</span>
                </div>
            </div>
        </div>

        <div class="login-form-side">
            <div class="login-form-inner">

                <div class="login-logo"><img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt=""></div>

                <h2 class="login-title">Chào mừng trở lại!</h2>
                <p class="login-sub">Đăng nhập để tiếp tục hành trình học tập</p>

                <div class="login-card">

                    <?php if ($error): ?>
                        <div class="form-error-box"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?page=auth&action=login" method="POST">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-with-icon">
                                <span class="input-icon">✉️</span>
                                <input type="email" id="email" name="email" placeholder="Nhập email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <div class="input-with-icon">
                                <span class="input-icon">🔒</span>
                                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                    title="Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số">
                                <button type="button" class="toggle-password" aria-label="Hiện mật khẩu">👁️</button>
                            </div>
                        </div>

                        <div class="forgot-row">
                            <a href="<?= BASE_URL ?>/index.php?page=auth&action=reset">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="submit-btn">Bắt đầu ngay! 🚀</button>

                        <p class="signup-row">
                            Chưa có tài khoản? <a href="<?= BASE_URL ?>/index.php?page=auth&action=register">Đăng ký ngay</a>
                        </p>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <script>
        const toggleBtn = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                toggleBtn.textContent = isHidden ? '🙈' : '👁️';
            });
        }
    </script>

</body>

</html>