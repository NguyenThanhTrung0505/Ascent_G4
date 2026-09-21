<!-- lesson_detail_view.php -->
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lesson["title"] ?? 'Bài học'); ?> | Ascent</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/lesson-detail.css">
</head>

<body>

    <div class="page-wrap">
        <?php $classId = (int)$_GET['class_id']; ?>
        <a class="breadcrumb" href="index.php?page=student&action=topic&topic_id=<?php echo $lesson['chapter_id'] ?>&class_id=<?php echo $classId ?>">← Quay lại <?php echo htmlspecialchars($lesson["chapter_title"] ?? ''); ?></a>
        <h1 class="lesson-title"><?php echo htmlspecialchars($lesson["title"] ?? ''); ?></h1>
        <p class="lesson-sub">Môn học: <?php echo htmlspecialchars($lesson["subject"] ?? ''); ?></p>

        <main>
            <section class="content-card">
                <h2><?php echo htmlspecialchars($lesson["section1_title"] ?? ''); ?></h2>
                <p><?php echo htmlspecialchars($lesson["section1_content"] ?? ''); ?></p>

                <div class="highlight-box">
                    <div class="label"><?php echo htmlspecialchars($lesson["highlight_label"] ?? ''); ?></div>
                    <code><?php echo $lesson["highlight_content"] ?? ''; // Chứa sẵn <br> nên không escape 
                            ?></code>
                </div>

                <h2><?php echo htmlspecialchars($lesson["section2_title"] ?? ''); ?></h2>
                <p><?php echo htmlspecialchars($lesson["section2_content"] ?? ''); ?></p>

            </section>

            <aside class="sidebar">

                <button class="complete-btn" id="completeBtn" onclick="markComplete()">
                    Đánh dấu hoàn thành
                </button>
            </aside>
        </main>
    </div>

    <script>
        const items = document.querySelectorAll(".check-item");
        const totalItems = items.length;

        function countDone() {
            return document.querySelectorAll(".check-item.done").length;
        }

        function updateProgress() {
            const done = countDone();
            const percent = Math.round((done / totalItems) * 100);
            const progressPercent = document.getElementById("progressPercent");
            const progressFill = document.getElementById("progressFill");
            const completeBtn = document.getElementById("completeBtn");

            if (progressPercent) progressPercent.textContent = percent + "%";
            if (progressFill) progressFill.style.width = percent + "%";

            if (percent === 100 && completeBtn) {
                completeBtn.textContent = "Đã hoàn thành bài học ✓";
                completeBtn.disabled = true;
            }
        }
        items.forEach(item => {
            item.addEventListener("click", () => {
                if (item.classList.contains("locked")) return;
                item.classList.toggle("done");
                const icon = item.querySelector(".check-icon");
                if (icon) {
                    icon.textContent = item.classList.contains("done") ? "✓" : "📖";
                }
                updateProgress();
            });
        });

        window.markComplete = function() {
            const completeBtn = document.getElementById("completeBtn");
            fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        action: "mark_done"
                    })
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        items.forEach(item => {
                            if (!item.classList.contains("locked")) {
                                item.classList.add("done");
                                const icon = item.querySelector(".check-icon");
                                if (icon) icon.textContent = "✓";
                            }
                        });
                        updateProgress();
                        if (completeBtn) {
                            completeBtn.textContent = "Đã hoàn thành bài học ✓";
                            completeBtn.disabled = true;
                            completeBtn.style.backgroundColor = "#4caf50";
                        }
                    } else {
                        alert("Lỗi: " + result.error);
                    }
                })
                .catch(err => {
                    console.error("Lỗi:", err);
                    alert("Lỗi kết nối mạng!");
                });
        };

        <?php if ($lesson['is_completed']): ?>
            const btn = document.getElementById("completeBtn");
            if (btn) {
                btn.textContent = "Đã hoàn thành bài học ✓";
                btn.disabled = true;
                btn.style.backgroundColor = "#4caf50";
            }
        <?php endif; ?>
        updateProgress();
    </script>

</body>

</html>