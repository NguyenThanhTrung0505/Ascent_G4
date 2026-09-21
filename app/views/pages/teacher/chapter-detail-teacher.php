<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bài học</title>
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
    </style>
</head>

<body>
    <main class="dashboard">
        <div class="chapter-topbar">
            <a href="index.php?page=teacher&action=class-detail&class_id=<?= $classId; ?>" class="back-link">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Quay lại
            </a>
            <div class="chapter-header">
                <div class="chapter-header__left">
                    <h1 class="chapter-header__title"><?= htmlspecialchars($tenChuong); ?></h1>
                    <p class="chapter-header__subtitle"><?= htmlspecialchars($tenLop); ?></p>
                </div>
                <button type="button" class="chapter-header__btn" id="btn-add-lesson">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Thêm bài
                </button>
            </div>
        </div>

        <section class="lessons-section">
            <h2 class="lessons-section__title">Danh sách bài học</h2>

            <?php if (empty($lessons)): ?>
                <div class="lessons-empty">
                    <span class="lessons-empty__icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <h3 class="lessons-empty__title">Chưa có bài học nào</h3>
                    <p class="lessons-empty__desc">Hãy thêm bài học đầu tiên cho chương này.</p>
                </div>
            <?php else: ?>
                <div class="lessons-list">
                    <?php foreach ($lessons as $bai): ?>
                        <article class="lesson-card">
                            <span class="lesson-card__icon"><i class="fa-solid fa-book-open"></i></span>
                            <div class="lesson-card__body">
                                <h3 class="lesson-card__title"><?= htmlspecialchars($bai['lesson_name'] ?? ''); ?></h3>
                                <p class="lesson-card__desc"><?= htmlspecialchars($bai['lesson_description'] ?? ''); ?></p>
                            </div>
                            <div class="lesson-card__actions">
                                <button type="button" class="lesson-card__menu-btn">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <div class="lesson-dropdown" role="menu">
                                    <button type="button" class="lesson-dropdown__item btn-edit"
                                        data-id="<?= $bai['lesson_id'] ?>"
                                        data-name="<?= htmlspecialchars($bai['lesson_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-desc="<?= htmlspecialchars($bai['lesson_description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        data-order="<?= $bai['order_index'] ?>"
                                        data-filename="<?= htmlspecialchars($bai['file_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fa-solid fa-pen"></i> Sửa thông tin
                                    </button>
                                    <textarea id="hidden-content-<?= $bai['lesson_id'] ?>" style="display: none;"><?= htmlspecialchars($bai['content'] ?? '') ?></textarea>

                                    <button type="button" class="lesson-dropdown__item lesson-dropdown__item--danger btn-delete"
                                        data-id="<?= $bai['lesson_id'] ?>">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="index.php?page=chapter-detail&class_id=<?= $classId ?>&chapter_id=<?= $chapterId ?>&p=<?= $page - 1 ?>">Trước</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="index.php?page=chapter-detail&class_id=<?= $classId ?>&chapter_id=<?= $chapterId ?>&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="index.php?page=chapter-detail&class_id=<?= $classId ?>&chapter_id=<?= $chapterId ?>&p=<?= $page + 1 ?>">Sau</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <div class="lesson-modal" id="lesson-modal" aria-hidden="true">
        <div class="lesson-modal__dialog">
            <div class="lesson-modal__header">
                <h2 id="lesson-modal-title">Thêm bài học mới</h2>
                <button type="button" class="lesson-modal__close">&times;</button>
            </div>

            <form class="lesson-form" id="form-lesson" enctype="multipart/form-data">
                <input type="hidden" name="action" id="form-action" value="add">
                <input type="hidden" name="lesson_id" id="form-lesson-id" value="">

                <section class="lesson-form__section">
                    <h3 class="lesson-form__heading"><span>1</span> Thông tin bài học</h3>
                    <div class="lesson-form__field">
                        <label>Tên bài học <span>*</span></label>
                        <input id="lesson-name" name="ten_bai_hoc" type="text" required>
                    </div>
                    <div class="lesson-form__field">
                        <label>Mô tả bài học</label>
                        <textarea id="lesson-description" name="mo_ta_bai_hoc" maxlength="200"></textarea>
                    </div>
                    <div class="lesson-form__field">
                        <label>Thứ tự hiển thị</label>
                        <input id="lesson-order" name="thu_tu" type="number" min="1" value="1">
                    </div>
                </section>

                <section class="lesson-form__section lesson-form__section--content">
                    <h3 class="lesson-form__heading"><span>2</span> Nội dung bài học</h3>
                    <div class="lesson-form__field">
                        <label>Nội dung bài học <span>*</span></label>
                        <textarea id="lesson-content" name="noi_dung" required style="width: 100%; min-height: 150px; padding: 10px;"></textarea>
                    </div>
                    <div class="lesson-form__field">
                        <label for="lesson-file">Tài liệu đính kèm <small>(tùy chọn)</small></label>
                        <div class="lesson-upload" style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 8px;">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 24px; color: #888;"></i>
                            <p style="margin: 10px 0;">Kéo thả tệp vào đây hoặc</p>
                            <label for="lesson-file" style="cursor: pointer; background: #eee; padding: 6px 12px; border-radius: 4px;">Chọn tệp</label>
                            <input id="lesson-file" name="tai_lieu" type="file" style="display:none;">
                            <p id="file-selected-name" style="margin-top: 10px; color: #28a745; font-weight: bold;"></p>
                        </div>
                        <small>Hỗ trợ: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG (Tối đa 20MB)</small>
                        <div id="current-file-display" style="display:none; margin-top: 10px; padding: 8px; background: #e3f2fd; border-radius: 4px;">
                            <i class="fa-solid fa-paperclip"></i> File hiện tại: <strong id="current-file-name"></strong>
                        </div>
                    </div>
                </section>

                <div class="lesson-modal__footer">
                    <button type="button" class="lesson-modal__cancel">Hủy</button>
                    <button type="submit" class="lesson-modal__submit">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu bài học
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('lesson-modal');
            const form = document.getElementById('form-lesson');
            const openButton = document.getElementById('btn-add-lesson');
            const closeButton = modal.querySelector('.lesson-modal__close');
            const cancelButton = modal.querySelector('.lesson-modal__cancel');
            const submitButton = form.querySelector('.lesson-modal__submit');

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            };
            document.getElementById('lesson-file').addEventListener('change', function(e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : '';
                document.getElementById('file-selected-name').textContent = fileName ? 'Tệp đã chọn: ' + fileName : '';
            });

            const openModal = (type = 'add', data = {}) => {
                form.reset();
                document.getElementById('form-action').value = type;
                document.getElementById('file-selected-name').textContent = '';
                document.getElementById('current-file-display').style.display = 'none';
                if (type === 'edit') {
                    document.getElementById('lesson-modal-title').textContent = "Sửa thông tin bài học";
                    document.getElementById('form-lesson-id').value = data.id;
                    document.getElementById('lesson-name').value = data.name;
                    document.getElementById('lesson-description').value = data.desc;
                    document.getElementById('lesson-order').value = data.order;
                    document.getElementById('lesson-content').value = data.content;
                    if (data.filename) {
                        document.getElementById('current-file-display').style.display = 'block';
                        document.getElementById('current-file-name').textContent = data.filename;
                    }
                } else {
                    document.getElementById('lesson-modal-title').textContent = "Thêm bài học mới";
                    document.getElementById('form-lesson-id').value = "";
                }

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            };

            openButton.addEventListener('click', () => openModal('add'));
            closeButton.addEventListener('click', closeModal);
            cancelButton.addEventListener('click', closeModal);

            document.querySelectorAll('.lesson-card__menu-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
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
                    const lessonId = btn.dataset.id;
                    const data = {
                        id: lessonId,
                        name: btn.dataset.name,
                        desc: btn.dataset.desc,
                        order: btn.dataset.order,
                        filename: btn.dataset.filename,
                        content: document.getElementById('hidden-content-' + lessonId).value
                    };

                    openModal('edit', data);
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (confirm('Bạn có chắc chắn muốn xóa bài học này không?')) {
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('lesson_id', btn.dataset.id);

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
                const formData = new FormData(form);

                fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            closeModal();
                            window.location.reload();
                        }
                    })
                    .catch(error => alert("Đã xảy ra lỗi hệ thống!"));
            });
        })();
    </script>
</body>

</html>