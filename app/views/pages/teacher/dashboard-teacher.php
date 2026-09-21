<?php

$so_chuong_hoc       = $so_chuong_hoc ?? 0;
$so_de_thi           = $so_de_thi ?? 0;
$so_lop_hoc          = $so_lop_hoc ?? 0;
$so_nhiem_vu_hom_nay = $so_nhiem_vu_hom_nay ?? 0;
$danh_sach_bai_tap   = $danh_sach_bai_tap ?? [];
$escape       = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$format_count = static fn($value): string => number_format(max(0, (int) $value), 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển - Giáo viên</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <!-- link -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">

</head>

<body>
    <?php require_once __DIR__ . '/../../layouts/header.php' ?>
    <main class="dashboard">
        <section class="stats-grid" aria-label="Thống kê nhanh">
            <a href="index.php?page=teacher&action=task-today" class="stat-card stat-card--link">
                <span class="stat-card__icon stat-card__icon--orange" aria-hidden="true"><i
                        class="fa-solid fa-book-open"></i></span>
                <h2 class="stat-card__title">Chương học</h2>
                <p class="stat-card__value"><?= $format_count($so_chuong_hoc); ?> chương</p>
            </a>

            <a href="index.php?page=teacher&action=task-today" class="stat-card stat-card--link">
                <span class="stat-card__icon stat-card__icon--blue" aria-hidden="true"><i
                        class="fa-regular fa-folder-open"></i></span>
                <h2 class="stat-card__title">Đề thi</h2>
                <p class="stat-card__value"><?= $format_count($so_de_thi); ?> bài thi</p>
            </a>

            <a href="index.php?page=teacher&action=many-class" class="stat-card stat-card--link">
                <span class="stat-card__icon stat-card__icon--green" aria-hidden="true"><i
                        class="fa-solid fa-graduation-cap"></i></span>
                <h2 class="stat-card__title">Lớp học</h2>
                <p class="stat-card__value"><?= $format_count($so_lop_hoc); ?> lớp</p>
            </a>

            <a href="index.php?page=teacher&action=task-today" class="stat-card stat-card--link">
                <span class="stat-card__icon stat-card__icon--pink" aria-hidden="true"><i
                        class="fa-regular fa-clock"></i></span>
                <h2 class="stat-card__title">Nhiệm vụ hôm nay</h2>
                <p class="stat-card__value"><?= $format_count($so_nhiem_vu_hom_nay); ?> bài thi</p>
            </a>
        </section>
        <section class="main-grid">
            <div class="create-panel">
                <h2 class="index.php?page=teacher&action=many-class">Tạo Đề Mới <i class="fa-solid fa-wand-magic-sparkles"
                        aria-hidden="true"></i></h2>
                <p class="create-panel__subtitle">Chọn cách bạn muốn tạo đề thi</p>

                <div class="create-panel__options">
                    <a href="./create-question-teacher.php" class="create-option">
                        <span class="create-option__icon" aria-hidden="true"><i class="fa-solid fa-pen"></i></span>
                        <span class="create-option__title">Gõ Nội Dung</span>
                        <span class="create-option__desc">Tự tạo câu hỏi và đáp án</span>
                    </a>
                </div>
            </div>

            <aside class="quick-actions">
                <h2 class="quick-actions__title">Thao tác nhanh</h2>
                <div class="quick-actions__list">
                    <a href=""><button type="button" class="quick-actions__btn" style="width: 100%;"><i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Thêm bài tập</button></a>
                    <a href=""><button type="button" class="quick-actions__btn" style="width: 100%;"><i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Thêm lớp</button></a>
                    <a href=""><button type="button" class="quick-actions__btn" style="width: 100%;"><i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Thêm học sinh mới</button></a>
                </div>
            </aside>
        </section>
        <section class="ongoing-section">
            <div class="ongoing-section__header">
                <h2 class="ongoing-section__title">Bài tập và đề thi đang diễn ra</h2>
                <a href="index.php?page=teacher&action=task-today" class="ongoing-section__view-all">
                    Xem tất cả <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <?php if (empty($danh_sach_bai_tap)): ?>
                <div class="ongoing-empty" role="status">
                    <i class="fa-regular fa-clipboard" aria-hidden="true"></i>
                    <p>Chưa có bài tập/đề thi nào đang diễn ra</p>
                    <a href="index.php?page=teacher&action=many-class" class="ongoing-empty__link">Tạo đề mới</a>
                </div>
            <?php else: ?>
                <div class="ongoing-grid">
                    <?php foreach ($danh_sach_bai_tap as $bai_tap):
                        $type_color = in_array($bai_tap['mau_the_loai'] ?? '', ['orange', 'cyan', 'green'], true)
                            ? $bai_tap['mau_the_loai']
                            : 'orange';
                        $completed = max(0, (int) ($bai_tap['so_da_hoan_thanh'] ?? 0));
                        $total = max(0, (int) ($bai_tap['tong_so_hoc_sinh'] ?? 0));
                        $percentage = $total > 0 ? min(100, max(0, $completed / $total * 100)) : 0;
                        $percentage_label = (int) round($percentage);
                    ?>
                        <article class="task-card">
                            <span class="task-card__tag task-card__tag--<?= $type_color; ?>">
                                <?= $escape($bai_tap['nhan_the_loai'] ?? 'Kiểm tra'); ?>
                            </span>

                            <h3 class="task-card__title"><?= $escape($bai_tap['tieu_de'] ?? 'Kiểm tra 15p - Chương 1'); ?></h3>
                            <p class="task-card__class"><?= $escape($bai_tap['ten_lop'] ?? 'Toán 6A'); ?></p>

                            <div class="task-card__progress">
                                <div class="progress-bar">
                                    <div class="progress-bar__fill progress-bar__fill--<?= $type_color; ?>"
                                        style="width: <?= $percentage; ?>%"></div>
                                </div>
                                <span class="task-card__percent"><?= $percentage_label; ?>%</span>
                            </div>

                            <p class="task-card__completed">
                                Đã hoàn thành: <?= $completed; ?>/<?= $total; ?>
                            </p>

                            <div class="task-card__footer">
                                <span class="task-card__date">
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    Ngày kết thúc: <?= $escape($bai_tap['ngay_ket_thuc'] ?? '02/07/2026'); ?>
                                </span>
                                <a href="<?= $escape($bai_tap['link_chi_tiet'] ?? '#'); ?>"
                                    class="task-card__link task-card__link--<?= $type_color; ?>">
                                    Xem chi tiết <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
</body>

</html>