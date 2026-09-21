<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lớp học của bạn | Ascent</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/many-class.css">
</head>

<body>

    <header class="main-header">
        <div class="logo-area">
            <img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt="Ascent">
            <span>Ascent</span>
        </div>
        <nav class="nav-menu">
            <a href="index.php?page=student&action=dashboard">Trang chủ</a>
            <a href="index.php?page=student&action=many-class" class="active">Lớp học</a>
            <a href="index.php?page=auth&action=logout">Đăng xuất</a>
        </nav>
        <div class="user-profile">
            <div class="user-info-text">
                <div class="user-name"><?php echo $studentName ?></div>
                <div class="user-role">Học sinh</div>
            </div>
            <div class="user-avatar"></div>
        </div>
    </header>

    <main class="page-container">
        <div class="page-header-title">
            <div class="title-wrap">
                <h1>Lớp học của bạn</h1>
                <p>Các lớp học bạn đang tham gia</p>
            </div>
            <form id="joinClassForm" style="display: flex; gap: 8px; margin: 0;">
                <input type="text" id="classCodeInput" placeholder="Nhập mã lớp..." required
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; min-width: 150px;">
                <button type="submit" class="join-btn" id="btnJoinClass" disabled>
                    + Tham gia
                </button>
            </form>
        </div>

        <div class="toolbar">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" id="searchInput" placeholder="Tìm kiếm">
            </div>
            <div class="filter-box">
                <span>🝖</span>
                <select id="filterSelect">
                    <option value="name">Lọc theo tên</option>
                    <option value="recent">Lọc theo ngày tạo</option>
                </select>
            </div>
        </div>

        <div class="class-grid" id="classGrid">

        </div>
    </main>

    <script>
        const classesData = <?php echo $classesDataJson ?? '[]'; ?>;

        function renderClasses() {
            const grid = document.getElementById("classGrid");
            const keyword = document.getElementById("searchInput").value.trim().toLowerCase();

            const filteredData = classesData.filter(c =>
                c.name.toLowerCase().includes(keyword) ||
                c.teacher.toLowerCase().includes(keyword) ||
                c.category.toLowerCase().includes(keyword)
            );

            if (filteredData.length === 0) {
                grid.innerHTML = `<div style="grid-column: 1/-1; color: var(--text-foreground); padding: 20px 0;">Không tìm thấy lớp học phù hợp.</div>`;
                return;
            }

            grid.innerHTML = filteredData.map(c => `
            <div class="class-card theme-${c.theme}">
                <div class="badge">
                    <span>${c.icon}</span> ${c.category}
                </div>
                <div class="card-title">${c.name}</div>
                <div class="card-footer">
                    <div class="teacher-name">
                        <span>👨‍🏫</span> ${c.teacher}
                    </div>
                    <button class="enter-btn" onclick="location.href='index.php?page=student&action=class&class_id=${c.id}'">Vào lớp</button>
                </div>
            </div>
        `).join("");
        }
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            searchInput.addEventListener("input", renderClasses);
        }
        document.addEventListener("DOMContentLoaded", renderClasses);
        const joinClassForm = document.getElementById('joinClassForm');
        const classCodeInput = document.getElementById('classCodeInput');
        const btnJoinClass = document.getElementById('btnJoinClass');
        classCodeInput?.addEventListener('input', (e) => {
            btnJoinClass.disabled = !e.target.value.trim();
        });
        joinClassForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const classCode = classCodeInput.value.trim();
            if (!classCode) return;
            const formData = new FormData();
            formData.append('class_code', classCode);
            fetch('index.php?page=student&action=many-class&api=join-class', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Tham gia lớp học thành công!');
                        window.location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Lỗi:', err);
                    alert('Lỗi kết nối máy chủ!');
                });
        });
    </script>

</body>

</html>