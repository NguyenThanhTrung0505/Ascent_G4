<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter["title"] ?? 'Chương học'); ?> | Ascent</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/topic.css">
</head>

<body>
    <div class="page-wrap">
        <?php $classId = (int)$_GET['class_id']; ?>
        <a class="breadcrumb" style="cursor: pointer;" href="index.php?page=student&action=class&class_id=<?= $classId ?> ">← Quay lại <?php echo htmlspecialchars($chapter["className"] ?? ''); ?></a>

        <div class="chapter-header">
            <div>
                <h1 class="chapter-title"><?php echo htmlspecialchars($chapter["title"] ?? 'Tên chương'); ?></h1>
                <div class="chapter-sub">Môn học: <?php echo htmlspecialchars($chapter["subject"] ?? 'Tên môn học'); ?></div>
            </div>
            <div class="progress-note">
                Hoàn thành chương: <strong id="progressText"><?php echo (int)($doneCount ?? 0); ?>/<?php echo (int)($totalCount ?? 0); ?> bài</strong>
            </div>
        </div>

        <h2 class="section-title">Danh sách bài học</h2>
        <div class="lesson-list" id="lessonList"></div>
    </div>

    <script>
        let lessons = <?php echo $lessonsJson ?? '[]'; ?>;

        function renderLessons() {
            const list = document.getElementById("lessonList");
            if (!list) return;

            list.innerHTML = lessons.map(l => `
                <div class="lesson-item">
                    <div class="lesson-icon ${l.done ? 'done' : 'pending'}">${l.done ? '✓' : '📖'}</div>
                    <div class="lesson-info">
                    <div class="lesson-title">${l.title}</div>
                    <div class="lesson-desc">${l.desc}</div>
                    </div>
                    <?php $classId = (int)$_GET['class_id']; ?>
                    <button class="lesson-status ${l.done ? 'done' : 'pending'}" onclick="window.location.href='index.php?page=student&action=lesson&id=${l.id}&topic_id=<?php echo $topicId; ?>&class_id=<?= $classId ?>'">
                    ${l.done ? 'Đã học' : 'Học ngay'}
                    </button>
                    <button class="more-btn" onclick="window.toggleMenu(event, ${l.id})">⋯</button>
                    <div class="more-menu" id="menu-${l.id}">
                    <button class="${l.done ? 'active-option' : ''}" onclick="window.setDone(${l.id}, true)">✓ Đánh dấu đã học</button>
                    <button class="${!l.done ? 'active-option' : ''}" onclick="window.setDone(${l.id}, false)">○ Đánh dấu chưa học</button>
                    </div>
                </div>
                `).join("");

            const doneCount = lessons.filter(l => l.done).length;
            const progText = document.getElementById("progressText");
            if (progText) progText.textContent = `${doneCount}/${lessons.length} bài`;
        }

        window.goToLesson = function(id) {
            location.href = `lesson_detail.php?id=${id}`;
        };

        window.toggleMenu = function(event, id) {
            event.stopPropagation();
            document.querySelectorAll(".more-menu").forEach(menu => {
                if (menu.id !== `menu-${id}`) menu.classList.remove("show");
            });

            const targetMenu = document.getElementById(`menu-${id}`);
            if (targetMenu) targetMenu.classList.toggle("show");
        };

        window.setDone = function(id, doneValue) {
            const lesson = lessons.find(l => l.id === id);
            if (!lesson) return;
            lesson.done = doneValue;
            renderLessons();
            fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        lesson_id: id,
                        done: doneValue
                    })
                })
                .then(res => res.json())
                .then(result => {
                    if (!result.success) {
                        console.error("Lưu thất bại:", result.error);
                        lesson.done = !doneValue;
                        renderLessons();
                        alert("Không lưu được tiến độ: " + result.error);
                    }
                })
                .catch(err => {
                    console.error("Lỗi kết nối:", err);
                    lesson.done = !doneValue;
                    renderLessons();
                    alert("Lỗi kết nối, không thể cập nhật tiến độ.");
                });
        };

        document.addEventListener("click", () => {
            document.querySelectorAll(".more-menu").forEach(menu => menu.classList.remove("show"));
        });

        renderLessons();
    </script>

</body>

</html>