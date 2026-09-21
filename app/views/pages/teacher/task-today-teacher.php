<?php
$today_tasks = $today_tasks ?? [];
$today_exams = $today_exams ?? [];
$today_chapters = $today_chapters ?? [];
$examPage = $examPage ?? 1;
$totalExamPages = $totalExamPages ?? 1;
$chapterPage = $chapterPage ?? 1;
$totalChapterPages = $totalChapterPages ?? 1;
$baseUrl = "index.php?page="
    . ($_GET['page'] ?? 'teacher')
    . "&action="
    . ($_GET['action'] ?? 'task-today');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng tổng hợp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <style>
        .today-pagination__item {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../layouts/header.php' ?>
    <main class="dashboard today-page">
        <section class="today-section">
            <div class="today-section__header">
                <h1 class="today-section__title">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i> Nhiệm vụ hôm nay
                </h1>
                <!-- <a href="index.php?page=bai-tap" class="today-section__link">
                    Xem tất cả nhiệm vụ <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a> -->
            </div>
            <div class="today-task-list">
                <?php foreach ($today_tasks as $task): ?>
                    <article class="today-task-card">
                        <div class="today-task-card__top">
                            <span class="today-task-card__icon" aria-hidden="true"><?= $task['icon']; ?></span>
                            <div>
                                <strong class="today-task-card__name"><?= $task['title']; ?></strong>
                                <span class="today-task-card__chapter"><?= $task['chapter']; ?></span>
                            </div>
                        </div>
                        <span class="today-task-card__deadline today-task-card__deadline--<?= $task['status']; ?>">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i> <?= $task['deadline']; ?>
                        </span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="today-section">
            <div class="today-section__header">
                <h2 class="today-section__title">Danh sách đề thi</h2>
            </div>
            <div class="today-list">
                <?php foreach ($today_exams as $exam): ?>
                    <article class="today-list-card">
                        <span class="today-list-card__icon" aria-hidden="true">
                            <i class="fa-regular fa-clipboard"></i>
                        </span>
                        <div class="today-list-card__body">
                            <h3 class="today-list-card__title"><?= $exam['title']; ?></h3>
                            <p class="today-list-card__meta"><?= $exam['meta']; ?></p>
                        </div>
                        <button type="button" class="today-list-card__menu" aria-label="Tùy chọn đề thi">
                            <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($totalExamPages > 1): ?>
                <nav class="today-pagination" aria-label="Phân trang danh sách đề thi">
                    <?php if ($examPage > 1): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $examPage - 1 ?>&chapter_page=<?= $chapterPage ?>" class="today-pagination__item today-pagination__item--arrow" aria-label="Trang trước">&lsaquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalExamPages; $i++): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $i ?>&chapter_page=<?= $chapterPage ?>" class="today-pagination__item <?= ($i == $examPage) ? 'today-pagination__item--active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($examPage < $totalExamPages): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $examPage + 1 ?>&chapter_page=<?= $chapterPage ?>" class="today-pagination__item today-pagination__item--arrow" aria-label="Trang sau">&rsaquo;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>

        <section class="today-section">
            <div class="today-section__header">
                <h2 class="today-section__title">Danh sách chương</h2>
            </div>
            <div class="today-list">
                <?php foreach ($today_chapters as $chapter): ?>
                    <article class="today-list-card">
                        <span class="today-list-card__icon" aria-hidden="true">
                            <i class="fa-solid fa-folder"></i>
                        </span>
                        <div class="today-list-card__body">
                            <h3 class="today-list-card__title"><?= $chapter['title']; ?></h3>
                            <p class="today-list-card__meta"><?= $chapter['meta']; ?></p>
                        </div>
                        <button type="button" class="today-list-card__menu" aria-label="Tùy chọn chương">
                            <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($totalChapterPages > 1): ?>
                <nav class="today-pagination" aria-label="Phân trang danh sách chương">
                    <?php if ($chapterPage > 1): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $examPage ?>&chapter_page=<?= $chapterPage - 1 ?>" class="today-pagination__item today-pagination__item--arrow" aria-label="Trang trước">&lsaquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalChapterPages; $i++): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $examPage ?>&chapter_page=<?= $i ?>" class="today-pagination__item <?= ($i == $chapterPage) ? 'today-pagination__item--active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($chapterPage < $totalChapterPages): ?>
                        <a href="<?= $baseUrl ?>&exam_page=<?= $examPage ?>&chapter_page=<?= $chapterPage + 1 ?>" class="today-pagination__item today-pagination__item--arrow" aria-label="Trang sau">&rsaquo;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>
    </main>
    <?php require_once __DIR__ . '/../../layouts/footer.php' ?>
</body>

</html>