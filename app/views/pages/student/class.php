<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($classData["info"]["className"] ?? 'Lớp học'); ?> | Ascent</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/class.css">
</head>

<body>
    <header class="page-header">
        <div class="header-content">
            <a href="index.php?page=student&action=many-class" class="back-link">← Quay lại danh sách lớp</a>
            <div class="header-main">
                <div class="class-info-wrap">
                    <div class="class-icon">📚</div>
                    <div>
                        <h1 class="class-title" id="lblClassName">Đang tải...</h1>
                        <div class="teacher-info">
                            👨‍🏫 <span id="lblTeacherName">Đang tải...</span>
                        </div>
                    </div>
                </div>
                <!-- <button class="doc-btn">Tài liệu lớp</button> -->
            </div>
        </div>
    </header>

    <main class="main-content">

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon orange">📖</div>
                    <div class="stat-number" id="statChapters">0</div>
                </div>
                <div class="stat-label">Tổng số chương</div>
                <div class="stat-sub">Chương trình chuẩn</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon blue">📄</div>
                    <div class="stat-number" id="statLessons">0</div>
                </div>
                <div class="stat-label">Tổng bài học</div>
                <div class="stat-sub">Đã phân phối</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon green">📁</div>
                    <div class="stat-number" id="statLearned">0/0</div>
                </div>
                <div class="stat-label">Bài đã học</div>
                <div class="stat-sub" id="statRatio"></div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-icon red">⏱️</div>
                    <div class="stat-number" id="statScore">0.0</div>
                </div>
                <div class="stat-label">Điểm trung bình</div>
                <div class="stat-sub"></div>
            </div>
        </div>

        <h2 class="section-title">Bài thi và bài kiểm tra</h2>
        <div class="grid-2" id="examList">
        </div>

        <h2 class="section-title">Danh sách chương học</h2>
        <div class="grid-2" id="chapterList">
        </div>

    </main>

    <script>
        const classData = <?php echo $classDataJson ?? '{}'; ?>;

        function renderUI() {
            if (!classData.info) return;

            document.getElementById("lblClassName").textContent = classData.info.className;
            document.getElementById("lblTeacherName").textContent = classData.info.teacherName;

            const stats = classData.stats;
            document.getElementById("statChapters").textContent = stats.totalChapters;
            document.getElementById("statLessons").textContent = stats.totalLessons;
            document.getElementById("statLearned").textContent = `${stats.learnedLessons}/${stats.totalLessons}`;

            const ratio = Math.round((stats.learnedLessons / stats.totalLessons) * 100) || 0;
            document.getElementById("statRatio").textContent = `Đạt tỉ lệ ${ratio}%`;
            document.getElementById("statScore").textContent = stats.avgScore;

            const examList = document.getElementById("examList");
            examList.innerHTML = classData.exams.map(ex => `
            <div class="exam-card">
                <div class="exam-badge">${ex.title}</div>
                <div class="exam-row">
                    <div class="exam-score-label">Điểm số: <span class="exam-score-value">${ex.score} / ${ex.maxScore}</span></div>
                    <div class="exam-status">${ex.status}</div>
                </div>
                <div class="exam-row">
                    <div class="exam-date">Ngày làm: ${ex.date}</div>
                    <a href="${ex.link}"><button class="exam-btn">Làm bài</button></a>
                </div>
            </div>
        `).join("");

            const chapterList = document.getElementById("chapterList");
            chapterList.innerHTML = classData.chapters.map(ch => {
                const pct = Math.round((ch.learned / ch.total) * 100) || 0;
                <?php $classId = (int)$_GET['class_id']; ?>
                return `
                <div class="chapter-card" style="cursor:pointer" onclick="location.href='index.php?page=student&action=topic&topic_id=${ch.id}&class_id=<?= $classId ?>'">
                    <div class="chap-top">
                        <div class="chap-icon">📘</div>
                        <div>
                            <div class="chap-title">${ch.title}</div>
                            <div class="chap-desc">${ch.desc}</div>
                        </div>
                    </div>
                    <div>
                        <div class="prog-row">
                            <span class="prog-label">Tiến độ cá nhân</span>
                            <span class="prog-value">${ch.learned}/${ch.total} Bài đã học (${pct}%)</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width: ${pct}%;"></div>
                        </div>
                    </div>
                </div>
            `;
            }).join("");
        }
        document.addEventListener("DOMContentLoaded", renderUI);
    </script>

</body>

</html>