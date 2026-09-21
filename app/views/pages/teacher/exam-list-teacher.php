<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Đề thi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
    <style>
        .pagination {
            display: flex;
            gap: 8px;
            margin-top: 20px;
            justify-content: center;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .exam-meta {
            font-size: 0.9rem;
            color: #666;
            margin-top: 8px;
            display: flex;
            gap: 15px;
        }

        .exam-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-link {
            text-decoration: none;
            color: inherit;
            width: 100%;
            display: block;
        }
    </style>
</head>

<body>
    <main class="dashboard">
        <div class="chapter-topbar">
            <a href="index.php?page=teacher&action=class-detail&class_id=<?= $classId ?>" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
            <div class="chapter-header">
                <div class="chapter-header__left">
                    <h1 class="chapter-header__title">Quản lý Đề thi</h1>
                    <p class="chapter-header__subtitle">Danh sách các bài kiểm tra đã tạo</p>
                </div>
                <a href="index.php?page=teacher&action=create-question&class_id=<?= $classId ?>" class="chapter-header__btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus"></i> Thêm đề thi
                </a>
            </div>
        </div>

        <section class="lessons-section">
            <?php if (empty($exams)): ?>
                <div class="lessons-empty">
                    <span class="lessons-empty__icon"><i class="fa-solid fa-file-circle-xmark"></i></span>
                    <h3 class="lessons-empty__title">Chưa có đề thi nào</h3>
                    <p class="lessons-empty__desc">Bạn chưa tạo đề thi nào. Hãy bắt đầu tạo đề thi mới nhé.</p>
                </div>
            <?php else: ?>
                <div class="lessons-list">
                    <?php foreach ($exams as $exam): ?>
                        <article class="lesson-card">
                            <span class="lesson-card__icon"><i class="fa-solid fa-file-signature"></i></span>

                            <div class="lesson-card__body">
                                <h3 class="lesson-card__title"><?= htmlspecialchars($exam['exam_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                                <div class="exam-meta">
                                    <span><i class="fa-regular fa-circle-question"></i> <?= $exam['total_questions'] ?> câu hỏi</span>
                                    <span><i class="fa-regular fa-clock"></i> <?= $exam['duration'] > 0 ? $exam['duration'] . ' phút' : 'Không giới hạn' ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($exam['created_at'])) ?></span>
                                </div>
                            </div>

                            <div class="lesson-card__actions">
                                <button type="button" class="lesson-card__menu-btn">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="lesson-dropdown" role="menu">
                                    <a href="index.php?page=teacher&action=update-question&class_id=<?= $classId ?>&exam_id=<?= $exam['exam_id'] ?>" class="lesson-dropdown__item dropdown-link">
                                        <i class="fa-solid fa-pen"></i> Chỉnh sửa đề
                                    </a>
                                    <button type="button" class="lesson-dropdown__item lesson-dropdown__item--danger btn-delete"
                                        data-id="<?= $exam['exam_id'] ?>">
                                        <i class="fa-solid fa-trash"></i> Xóa đề thi
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="index.php?page=exam-list&p=<?= $page - 1 ?>">Trước</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="index.php?page=exam-list&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="index.php?page=exam-list&p=<?= $page + 1 ?>">Sau</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

    <script>
        (() => {
            document.querySelectorAll('.lesson-card__menu-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const dropdown = btn.nextElementSibling;
                    document.querySelectorAll('.lesson-dropdown').forEach(d => {
                        if (d !== dropdown) d.classList.remove('is-open');
                    });
                    dropdown.classList.toggle('is-open');
                });
            });

            document.addEventListener('click', () => {
                document.querySelectorAll('.lesson-dropdown').forEach(d => d.classList.remove('is-open'));
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa đề thi này? Toàn bộ câu hỏi và đáp án bên trong sẽ bị xóa vĩnh viễn.')) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('exam_id', btn.dataset.id);

                        fetch(window.location.href, {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                alert(data.message);
                                if (data.success) {
                                    window.location.reload();
                                }
                            })
                            .catch(error => {
                                alert("Đã xảy ra lỗi hệ thống, không thể xóa!");
                                console.error(error);
                            });
                    }
                });
            });
        })();
    </script>
</body>

</html>