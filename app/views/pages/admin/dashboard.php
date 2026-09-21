<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan hệ thống | Ascent Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin/dashboard.css">
</head>

<body>

    <header>
        <a href="dashboard-admin.php" class="logo">
            <span><img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt=""></span>
            Ascent Admin
        </a>
        <nav>
            <a href="index.php?page=admin&action=dashboard" class="active">Trang chủ</a>
            <a href="index.php?page=admin&action=users">Quản lý người dùng</a>
            <a href="index.php?page=admin&action=class">Quản lý lớp</a>
            <a href="index.php?page=admin&action=exam">Quản lý đề thi</a>
            <a href="index.php?page=auth&action=logout">Đăng xuất</a>
        </nav>
        <div class="user-box">
            <div class="avatar">AA</div>
            <div class="user-info">
                <div class="user-name">Admin Ascent</div>
                <div class="user-role">Quản trị viên</div>
            </div>
        </div>
    </header>
    <div class="page-wrap">
        <h1 class="page-title">Tổng quan hệ thống</h1>
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon orange">🎓</div>
                <div class="stat-number" id="statTeachers">0</div>
                <div class="stat-label">Tổng giáo viên</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">👥</div>
                <div class="stat-number" id="statStudents">0</div>
                <div class="stat-label">Tổng học sinh</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">📁</div>
                <div class="stat-number" id="statClasses">0</div>
                <div class="stat-label">Tổng lớp học</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pink">⏰</div>
                <div class="stat-number" id="statExams">0</div>
                <div class="stat-label">Tổng đề thi</div>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="panel">
                <h2>Hoạt động gần đây</h2>
                <div id="activityList"></div>
            </div>
            <div class="panel">
                <h2>Thống kê tuần này</h2>
                <div class="bar-chart" id="barChart"></div>
                <div class="stat-note">Lượt truy cập hoạt động tích cực nhất vào Thứ 5 hằng tuần.</div>
                <div class="stat-total">Tổng cộng: 1,450 hoạt động</div>
            </div>
        </div>
    </div>

    <script>
        const stats = <?php echo $statsJson ?? '{}'; ?>;
        const activities = <?php echo $activitiesJson ?? '[]'; ?>;

        const weeklyData = <?php echo $weeklyDataJson ?? '[]'; ?>;
        const totalWeeklyActivities = <?php echo $totalActivitiesWeek ?? 0; ?>;

        const statTotalEl = document.querySelector(".stat-total");
        if (statTotalEl) {
            statTotalEl.textContent = `Tổng cộng: ${totalWeeklyActivities.toLocaleString('vi-VN')} hoạt động`;
        }

        function animateNumber(el, target) {
            let current = 0;
            const step = Math.ceil(target / 30) || 1;
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current;
            }, 25);
        }

        animateNumber(document.getElementById("statTeachers"), Number(stats.teachers));
        animateNumber(document.getElementById("statStudents"), Number(stats.students));
        animateNumber(document.getElementById("statClasses"), Number(stats.classes));
        animateNumber(document.getElementById("statExams"), Number(stats.exams));

        const activityList = document.getElementById("activityList");
        if (activities.length === 0) {
            activityList.innerHTML = `<p style="color:#6b7280; font-size:14.5px;">Chưa có hoạt động nào gần đây.</p>`;
        } else {
            activities.forEach(a => {
                const item = document.createElement("div");
                item.className = "activity-item";
                item.innerHTML = `
        <div>
          <div class="activity-name">${a.name}</div>
          <div class="activity-desc">${a.desc}</div>
        </div>
        <div class="activity-time">${a.time}</div>
      `;
                activityList.appendChild(item);
            });
        }

        const barChart = document.getElementById("barChart");
        const maxValue = Math.max(...weeklyData.map(d => d.value));
        weeklyData.forEach(d => {
            const col = document.createElement("div");
            col.className = "bar-col";
            const heightPercent = (d.value / maxValue) * 100;
            col.innerHTML = `
      <div class="bar" style="height: ${heightPercent}%;"></div>
      <div class="bar-label">${d.label}</div>
    `;
            barChart.appendChild(col);
        });
    </script>

</body>

</html>