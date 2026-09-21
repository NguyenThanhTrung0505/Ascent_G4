<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Chương học</title>
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

        .chapter-meta {
            font-size: 0.9rem;
            color: #666;
            margin-top: 8px;
            display: flex;
            gap: 15px;
        }

        .chapter-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chapter-link {
            text-decoration: none;
            color: inherit;
            display: block;
            flex-grow: 1;
        }

        .chapter-link:hover .lesson-card__title {
            color: #007bff;
        }
    </style>
</head>

<body>
    <main class="dashboard">
        <div class="chapter-topbar">
            <a href="index.php?page=teacher&action=class-detail&class_id=<?= $classId; ?>" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
            <div class="chapter-header">
                <div class="chapter-header__left">
                    <h1 class="chapter-header__title">Danh sách Chương học</h1>
                    <p class="chapter-header__subtitle"><?= htmlspecialchars($tenLop); ?></p>
                </div>
                <button type="button" class="chapter-header__btn" id="btn-add-chapter">
                    <i class="fa-solid fa-plus"></i> Thêm chương
                </button>
            </div>
        </div>

        <section class="lessons-section">
            <?php if (empty($chapters)): ?>
                <div class="lessons-empty">
                    <span class="lessons-empty__icon"><i class="fa-solid fa-folder-open"></i></span>
                    <h3 class="lessons-empty__title">Chưa có chương nào</h3>
                    <p class="lessons-empty__desc">Hãy thêm chương học đầu tiên cho lớp này.</p>
                </div>
            <?php else: ?>
                <div class="lessons-list">
                    <?php foreach ($chapters as $chuong): ?>
                        <article class="lesson-card">
                            <span class="lesson-card__icon"><i class="fa-solid fa-folder"></i></span>

                            <a href="index.php?page=teacher&action=chapter-detail&class_id=<?= $classId ?>&chapter_id=<?= $chuong['chapter_id'] ?>" class="chapter-link">
                                <div class="lesson-card__body">
                                    <h3 class="lesson-card__title"><?= htmlspecialchars($chuong['chapter_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="lesson-card__desc"><?= htmlspecialchars($chuong['chapter_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="chapter-meta">
                                        <span><i class="fa-solid fa-book"></i> <?= $chuong['total_lessons'] ?> bài học</span>
                                        <span><i class="fa-solid fa-chart-pie"></i> Hoàn thành: <?= $chuong['progress_percentage'] ?>%</span>
                                    </div>
                                </div>
                            </a>

                            <div class="lesson-card__actions">
                                <button type="button" class="lesson-card__menu-btn">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="lesson-dropdown" role="menu">
                                    <button type="button" class="lesson-dropdown__item btn-edit"
                                        data-id="<?= $chuong['chapter_id'] ?>"
                                        data-name="<?= htmlspecialchars($chuong['chapter_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-title="<?= htmlspecialchars($chuong['chapter_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-order="<?= $chuong['order_index'] ?>">
                                        <i class="fa-solid fa-pen"></i> Sửa thông tin
                                    </button>
                                    <button type="button" class="lesson-dropdown__item lesson-dropdown__item--danger btn-delete"
                                        data-id="<?= $chuong['chapter_id'] ?>">
                                        <i class="fa-solid fa-trash"></i> Xóa chương
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="index.php?page=chapter-list&class_id=<?= $classId ?>&p=<?= $page - 1 ?>">Trước</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="index.php?page=chapter-list&class_id=<?= $classId ?>&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="index.php?page=chapter-list&class_id=<?= $classId ?>&p=<?= $page + 1 ?>">Sau</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

    <!-- Modal Form Chương -->
    <div class="lesson-modal" id="chapter-modal" aria-hidden="true">
        <div class="lesson-modal__dialog">
            <div class="lesson-modal__header">
                <h2 id="modal-title">Thêm chương mới</h2>
                <button type="button" class="lesson-modal__close">&times;</button>
            </div>

            <form class="lesson-form" id="form-chapter">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="chapter_id" id="form-chapter-id" value="">

                <section class="lesson-form__section">
                    <div class="lesson-form__field">
                        <label>Tên chương (VD: Chương 1, Tuần 1) <span>*</span></label>
                        <input id="chapter-name" name="ten_chuong" type="text" required>
                    </div>
                    <div class="lesson-form__field">
                        <label>Tiêu đề / Mô tả chương</label>
                        <textarea id="chapter-title" name="tieu_de_chuong" maxlength="200"></textarea>
                    </div>
                    <div class="lesson-form__field">
                        <label>Thứ tự hiển thị</label>
                        <input id="chapter-order" name="thu_tu" type="number" min="1" value="1">
                    </div>
                </section>

                <div class="lesson-modal__footer">
                    <button type="button" class="lesson-modal__cancel">Hủy</button>
                    <button type="submit" class="lesson-modal__submit">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu chương
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('chapter-modal');
            const form = document.getElementById('form-chapter');
            const openButton = document.getElementById('btn-add-chapter');
            const closeButton = modal.querySelector('.lesson-modal__close');
            const cancelButton = modal.querySelector('.lesson-modal__cancel');
            const submitButton = form.querySelector('.lesson-modal__submit');

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            };

            const openModal = (type = 'add', data = {}) => {
                form.reset();
                document.getElementById('form-action').value = type;

                if (type === 'edit') {
                    document.getElementById('modal-title').textContent = "Sửa thông tin chương";
                    document.getElementById('form-chapter-id').value = data.id;
                    document.getElementById('chapter-name').value = data.name;
                    document.getElementById('chapter-title').value = data.title;
                    document.getElementById('chapter-order').value = data.order;
                } else {
                    document.getElementById('modal-title').textContent = "Thêm chương mới";
                    document.getElementById('form-chapter-id').value = "";
                }
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            };

            openButton.addEventListener('click', () => openModal('add'));
            closeButton.addEventListener('click', closeModal);
            cancelButton.addEventListener('click', closeModal);

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

            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    openModal('edit', btn.dataset);
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm('Bạn có chắc chắn muốn xóa chương này? Toàn bộ bài học bên trong có thể bị mất.')) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('chapter_id', btn.dataset.id);

                        fetch(window.location.href, {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                alert(data.message);
                                if (data.success) window.location.reload();
                            });
                    }
                });
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

                fetch(window.location.href, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            closeModal();
                            window.location.reload();
                        } else {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        alert("Đã xảy ra lỗi hệ thống!");
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
            });
        })();
    </script>
</body>

</html>