<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/dashboard.css">
</head>

<body>
    <header class="main-header">
        <div class="logo-area">
            <img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt="Ascent">
            <span>Ascent</span>
        </div>
        <nav class="nav-menu">
            <a href="index.php?page=student&action=dashboard" class="active">Trang chủ</a>
            <a href="index.php?page=student&action=many-class">Lớp học</a>
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

        <div class="stats-grid" id="statsGrid">
        </div>

        <div class="section-header">
            <h2>Bài tập và đề thi sắp đến hạn</h2>
        </div>

        <div class="tasks-grid" id="tasksGrid">
        </div>

    </main>

    <script>
        const dashboardData = <?php echo $dashboardDataJson ?? '{}'; ?>;

        function renderDashboard() {
            if (!dashboardData.stats || !dashboardData.upcomingTasks) return;
            const statsGrid = document.getElementById("statsGrid");
            statsGrid.innerHTML = dashboardData.stats.map(stat => `
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon icon-theme-${stat.theme}">${stat.icon}</div>
                    <div class="stat-number">${stat.value}</div>
                </div>
                <div class="stat-title">${stat.title}</div>
                <div class="stat-desc">${stat.desc}</div>
            </div>
        `).join("");

            const tasksGrid = document.getElementById("tasksGrid");
            tasksGrid.innerHTML = dashboardData.upcomingTasks.map(task => `
            <div class="task-card task-theme-orange">
                <div class="task-badge">${task.badge}</div>
                <div class="task-subject">${task.subject}</div>
                
                <div class="prog-row">
                    <span class="prog-label">Đã hoàn thành</span>
                    <span class="prog-value">${task.progress}%</span>
                </div>
                <div class="prog-track">
                    <div class="prog-fill" style="width: ${task.progress}%;"></div>
                </div>
                
                <div class="task-footer">
                    <div class="task-deadline">Hạn nộp: ${task.deadline}</div>
                </div>
            </div>
        `).join("");
        }
        document.addEventListener("DOMContentLoaded", renderDashboard())
    </script>

</body>

</html>