<body>
    <header class="header">
        <div class="header-container">
            <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Toggle navigation">
            <label for="menu-toggle" class="menu-btn">
                <span class="hamburger"></span>
            </label>
            <a href="index.php?page=teacher&action=dashboard" class="logo">
                <img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt="Ascent">
                <span class="logo-text">Ascent</span>
            </a>
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="index.php?page=teacher&action=dashboard" class="nav-link">Trang chủ</a></li>
                    <li class="nav-item"><a href="index.php?page=teacher&action=many-class" class="nav-link">Lớp học</a></li>
                    <li class="nav-item"><a href="index.php?page=teacher&action=task-today" class="nav-link">Nhiệm vụ hôm nay</a></li>
                    <li class="nav-item"><a href="index.php?page=auth&action=logout" class="nav-link">Đăng xuất</a></li>
                </ul>
            </nav>
            <label for="menu-toggle" class="menu-backdrop"></label>
            <div class="user-profile">
                <div class="avatar">NA</div>
                <div class="user-info">
                    <span class="user-name"><?= $teacherName ?></span>
                    <span class="user-role">Giáo viên</span>
                </div>
            </div>
        </div>
    </header>
    <script>
        const menuItems = document.querySelectorAll('.nav-link');
        const params = new URLSearchParams(window.location.search);
        const currentAction = params.get('action');

        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href) {
                const linkParams = new URLSearchParams(href.split('?')[1]);
                const linkAction = linkParams.get('action');
                if (linkAction === currentAction) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            }
        });
    </script>

</body>