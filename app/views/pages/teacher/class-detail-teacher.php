<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết lớp học</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
</head>

<body>
    <?php require_once __DIR__ . '/../../layouts/header.php' ?>
    <main class="dashboard">

        <div class="class-detail-topbar">
            <a href="index.php?page=teacher&action=many-class" class="back-link">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Quay lại danh sách lớp
            </a>

            <div class="class-detail-header">
                <div class="class-detail-header__left">
                    <span class="class-detail-header__icon" aria-hidden="true">
                        <?= $thong_tin_lop['icon_mon_hoc']; ?>
                    </span>
                    <div class="class-detail-header__meta">
                        <h1 class="class-detail-header__name">
                            <?= htmlspecialchars($thong_tin_lop['ten_lop']); ?>
                        </h1>
                        <span class="class-detail-header__students">
                            <i class="fa-solid fa-user-group" aria-hidden="true"></i>
                            <?= (int) $thong_tin_lop['so_hoc_sinh']; ?> học sinh
                        </span>
                    </div>
                </div>

                <?php if ($thong_tin_lop['id'] > 0): ?>
                    <div class="class-detail-header__actions">
                        <form id="addStudentForm" style="display: flex; gap: 5px; margin: 0;">
                            <input type="email" id="studentEmail" placeholder="Nhập email học sinh..." required
                                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; min-width: 200px;">
                            <button type="submit" class="btn-outline" id="btnSubmitAddStudent" disabled style="white-space: nowrap;">
                                <i class="fa-solid fa-user-plus" aria-hidden="true"></i> Thêm
                            </button>
                        </form>
                        <a href="index.php?page=teacher&action=create-question&class_id=<?= $id_lop; ?>" class="btn-outline">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i> Tạo Đề Thi
                        </a>
                        <button type="button" class="btn-solid-teal" id="btnOpenCreateChapterModal">
                            <i class="fa-regular fa-square-plus" aria-hidden="true"></i> Thêm Chương Mới
                        </button>


                    </div>
                <?php endif; ?>
            </div>
        </div>

        <section class="stats-grid" aria-label="Thống kê lớp học">
            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--orange"><i class="fa-solid fa-book-open"></i></span>
                <p class="stat-card__number"><?= (int) $thong_tin_lop['tong_so_chuong']; ?></p>
                <h2 class="stat-card__label">Tổng số chương</h2>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--blue"><i class="fa-regular fa-file-lines"></i></span>
                <p class="stat-card__number"><?= (int) $thong_tin_lop['tong_bai_hoc']; ?></p>
                <h2 class="stat-card__label">Tổng bài học</h2>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-folder"></i></span>
                <p class="stat-card__number"><?= (int) $thong_tin_lop['tong_bai_thi_kiem_tra']; ?></p>
                <h2 class="stat-card__label">Tổng bài kiểm tra</h2>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--pink"><i class="fa-regular fa-clock"></i></span>
                <p class="stat-card__number" style="font-size: 18px; line-height: 1.5; padding-top: 10px;">
                    <?= htmlspecialchars($thong_tin_lop['tien_do_lop']); ?>
                </p>
                <h2 class="stat-card__label">Tiến độ lớp</h2>
            </article>
        </section>

        <section class="ongoing-section">
            <div class="ongoing-section__header">
                <h2 class="ongoing-section__title">Bài thi và bài kiểm tra</h2>
                <a href="index.php?page=teacher&action=exam-list&class_id=<?= $id_lop; ?>" class="ongoing-section__view-all">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="exam-grid">
                <?php if (empty($thong_tin_lop['danh_sach_bai_kiem_tra'])): ?>
                    <p style="color: #666">Chưa có bài kiểm tra nào.</p>
                <?php else: ?>
                    <?php foreach ($thong_tin_lop['danh_sach_bai_kiem_tra'] as $bai_kt): ?>
                        <article class="exam-card">
                            <span class="exam-card__tag"><?= htmlspecialchars($bai_kt['tieu_de']); ?></span>
                            <div class="exam-card__progress-row">
                                <span class="exam-card__progress-label">Tiến độ</span>
                                <span class="exam-card__progress-percent"><?= (int)$bai_kt['phan_tram']; ?>%</span>
                            </div>
                            <div class="exam-card__progress-bar">
                                <div class="exam-card__progress-fill" style="width: <?= (int)$bai_kt['phan_tram']; ?>%"></div>
                            </div>
                            <p class="exam-card__completed">Đã hoàn thành: <?= $bai_kt['so_da_hoan_thanh']; ?>/<?= $bai_kt['tong_so_hoc_sinh']; ?></p>
                            <div class="exam-card__footer">
                                <span class="exam-card__date">Ngày kết thúc: <?= $bai_kt['ngay_ket_thuc']; ?></span>
                                <a href="index.php?page=teacher&action=update-question&class_id=<?= $id_lop ?>&exam_id=<?= $bai_kt['id'] ?>" class="exam-card__link">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="ongoing-section">
            <div class="ongoing-section__header">
                <h2 class="ongoing-section__title">Danh sách chương học</h2>
                <a href="index.php?page=teacher&action=chapter-list&class_id=<?= $id_lop; ?>" class="ongoing-section__view-all">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div class="chapter-grid">
                <?php if (empty($thong_tin_lop['danh_sach_chuong'])): ?>
                    <p style="color: #666">Chưa có chương học nào được tạo.</p>
                <?php else: ?>
                    <?php foreach ($thong_tin_lop['danh_sach_chuong'] as $chuong): ?>
                        <a href="<?= $chuong['link_chi_tiet']; ?>" class="chapter-card">
                            <span class="chapter-card__icon"><i class="fa-solid fa-book"></i></span>
                            <div class="chapter-card__body">
                                <h3 class="chapter-card__name"><?= htmlspecialchars($chuong['ten_chuong']); ?></h3>
                                <p class="chapter-card__desc"><?= htmlspecialchars($chuong['mo_ta']); ?></p>
                                <div class="chapter-card__progress-row">
                                    <span class="chapter-card__progress-label">Tiến độ học tập</span>
                                    <span class="chapter-card__progress-percent"><?= (int)$chuong['phan_tram']; ?>%</span>
                                </div>
                                <div class="chapter-card__progress-bar">
                                    <div class="chapter-card__progress-fill" style="width: <?= (int)$chuong['phan_tram']; ?>%"></div>
                                </div>
                                <div class="chapter-card__footer">
                                    <span class="chapter-card__footer-item"><i class="fa-regular fa-file-lines"></i> <?= (int)$chuong['so_bai_hoc']; ?> bài</span>
                                    <span class="chapter-card__footer-item chapter-card__footer-item--done"><i class="fa-solid fa-circle-check"></i> TB hoàn thành <?= (int)$chuong['so_bai_hoan_thanh']; ?> bài/HS</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <div class="modal-overlay" id="createChapterModal" aria-hidden="true">
        <div class="create-chapter-modal" role="dialog" aria-modal="true" aria-labelledby="createChapterModalTitle">
            <header class="create-chapter-modal__header">
                <div class="create-chapter-modal__heading">
                    <i class="fa-solid fa-book-open"></i>
                    <div>
                        <h2 id="createChapterModalTitle">Tạo Chương Mới</h2>
                        <p><?= htmlspecialchars($thong_tin_lop['ten_lop']); ?></p>
                    </div>
                </div>
                <button type="button" class="create-chapter-modal__close" id="btnCloseCreateChapterModal">&times;</button>
            </header>

            <form class="create-chapter-form" id="createChapterForm" method="post">
                <div class="create-chapter-form__body">


                    <div class="create-chapter-preview">
                        <span class="create-chapter-preview__icon" id="createChapterPreviewIcon">📊</span>
                        <div>
                            <strong id="createChapterPreviewTitle">Tên chương...</strong>
                            <p id="createChapterPreviewDescription">Mô tả chương...</p>
                        </div>
                    </div>

                    <div class="create-chapter-form__field">
                        <label for="createChapterName">Tên chương <span>*</span></label>
                        <input type="text" name="ten_chuong" id="createChapterName" placeholder="VD: Chương 5: Phương Trình..." required>
                    </div>
                    <div class="create-chapter-form__field">
                        <label for="createChapterDescription">Mô tả nội dung</label>
                        <textarea name="mo_ta" id="createChapterDescription" rows="3" placeholder="Ngắn gọn về nội dung chương học..."></textarea>
                    </div>
                </div>

                <footer class="create-chapter-modal__footer">
                    <button type="button" class="create-chapter-modal__cancel" id="btnCancelCreateChapterModal">Huỷ</button>
                    <button type="submit" class="create-chapter-modal__submit" id="btnSubmitCreateChapterModal" disabled>
                        <i class="fa-regular fa-floppy-disk"></i> Tạo chương
                    </button>
                </footer>
            </form>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../layouts/footer.php' ?>

    <script>
        (() => {
            const currentClassId = <?= $id_lop; ?>;
            const addStudentForm = document.getElementById('addStudentForm');
            const studentEmailInput = document.getElementById('studentEmail');
            const btnSubmitAddStudent = document.getElementById('btnSubmitAddStudent');
            const modal = document.getElementById('createChapterModal');
            const openButton = document.getElementById('btnOpenCreateChapterModal');
            const closeButton = document.getElementById('btnCloseCreateChapterModal');
            const cancelButton = document.getElementById('btnCancelCreateChapterModal');
            const form = document.getElementById('createChapterForm');
            const submitButton = document.getElementById('btnSubmitCreateChapterModal');

            const nameInput = document.getElementById('createChapterName');
            const descriptionInput = document.getElementById('createChapterDescription');
            const iconInput = document.getElementById('createChapterIcon');
            const previewIcon = document.getElementById('createChapterPreviewIcon');
            const previewTitle = document.getElementById('createChapterPreviewTitle');
            const previewDescription = document.getElementById('createChapterPreviewDescription');
            studentEmailInput?.addEventListener('input', (e) => {
                btnSubmitAddStudent.disabled = !e.target.value.trim();
            });
            addStudentForm?.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = studentEmailInput.value.trim();

                if (!email) return;

                const formData = new FormData();
                formData.append('class_id', currentClassId);
                formData.append('email', email);

                fetch(`index.php?page=teacher&action=class-detail&id=${currentClassId}&api=add-student`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Thêm học sinh thành công!');
                            window.location.reload();
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('Lỗi kết nối máy chủ!');
                    });
            });
            form?.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                formData.append('class_id', currentClassId);
                fetch(`index.php?page=teacher&action=class-detail&id=${currentClassId}&api=create-chapter`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('Lỗi kết nối máy chủ!');
                    });
            });

            const closeModal = () => {
                if (!modal) return;
                modal.classList.remove('is-visible');
                modal.setAttribute('aria-hidden', 'true');
            };

            openButton?.addEventListener('click', () => {
                modal.classList.add('is-visible');
                modal.setAttribute('aria-hidden', 'false');
                nameInput.focus();
            });

            closeButton?.addEventListener('click', closeModal);
            cancelButton?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            modal?.querySelectorAll('.create-chapter-icon-picker__item').forEach((btn) => {
                btn.addEventListener('click', () => {
                    modal.querySelector('.create-chapter-icon-picker__item.is-active')?.classList.remove('is-active');
                    btn.classList.add('is-active');
                    iconInput.value = btn.dataset.icon;
                    previewIcon.textContent = btn.dataset.icon;
                });
            });

            nameInput?.addEventListener('input', () => {
                const name = nameInput.value.trim();
                previewTitle.textContent = name || 'Tên chương...';
                submitButton.disabled = !name;
            });

            descriptionInput?.addEventListener('input', () => {
                previewDescription.textContent = descriptionInput.value.trim() || 'Mô tả chương...';
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });
        })();
    </script>
</body>

</html>