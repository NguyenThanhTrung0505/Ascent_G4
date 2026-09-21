<?php
$danh_sach_lop_hoc = !empty($danh_sach_lop_hoc) ? $danh_sach_lop_hoc : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lớp học</title>
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
        <section class="classes-header">
            <div class="classes-header__text">
                <h1 class="classes-header__title">Lớp học của bạn</h1>
                <p class="classes-header__subtitle">Quản lý các lớp học và học sinh của bạn</p>
            </div>
            <button type="button" class="classes-header__btn">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Thêm lớp học mới
            </button>
        </section>

        <section class="classes-toolbar">
            <label class="search-input">
                <i class="fa-solid fa-magnifying-glass search-input__icon" aria-hidden="true"></i>
                <input type="text" id="searchInput" name="tim_kiem_lop_hoc" placeholder="Tìm tên lớp, môn, mã lớp...">
            </label>
        </section>

        <section class="classes-grid" id="classesGrid" aria-label="Danh sách lớp học">
            <?php if (empty($danh_sach_lop_hoc)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: #666; padding: 2rem;">Chưa có lớp học nào. Hãy tạo lớp đầu tiên!</p>
            <?php else: ?>
                <?php foreach ($danh_sach_lop_hoc as $lop): ?>
                    <?php $mau = $lop['mau_chu_dao'] ?? 'orange'; ?>
                    <article class="class-card">
                        <div class="class-card__banner class-card__banner--<?= $mau; ?>">
                            <span class="class-card__icon" aria-hidden="true"><?= $lop['icon_mon_hoc'] ?? '📊'; ?></span>
                            <button type="button" class="class-card__favorite <?= !empty($lop['is_favorite']) ? 'class-card__favorite--active' : ''; ?>">
                                <i class="<?= !empty($lop['is_favorite']) ? 'fa-solid' : 'fa-regular'; ?> fa-star"></i>
                            </button>
                        </div>

                        <div class="class-card__body">
                            <div class="class-card__header">
                                <div style="display: flex; justify-content: space-between; width: 100%">
                                    <h2 class="class-card__name"><?= htmlspecialchars($lop['ten_lop'] ?? ''); ?></h2>
                                    <div class="class-card__menu-wrapper">
                                        <button type="button" class="class-card__menu-btn"
                                            data-id="<?= $lop['id'] ?? ''; ?>"
                                            data-name="<?= htmlspecialchars($lop['ten_lop'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            data-desc="<?= htmlspecialchars($lop['ten_mon_hoc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            data-icon="<?= htmlspecialchars($lop['icon_mon_hoc'] ?? '📊', ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="class-card__menu" role="menu">
                                            <div class="class-card__menu-code">
                                                Mã lớp: <strong><?= $lop['ma_lop'] ?? ''; ?></strong>
                                            </div>
                                            <button type="button" class="class-card__menu-item class-card__menu-item--edit">
                                                <i class="fa-solid fa-pen"></i> Sửa
                                            </button>
                                            <button type="button" class="class-card__menu-item class-card__menu-item--delete">
                                                <i class="fa-solid fa-trash"></i> Xóa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="class-card__footer">
                                    <span class="class-card__students">
                                        <i class="fa-solid fa-user-group"></i> <?= (int) ($lop['so_hoc_sinh'] ?? 0); ?> học sinh
                                    </span>
                                    <a href="index.php?page=teacher&action=class-detail&class_id=<?= $lop['id'] ?>" class="class-card__link class-card__link--<?= $mau; ?>">Xem chi tiết &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <div class="chapter-modal" id="chapter-modal" aria-hidden="true">
        <div class="chapter-modal__dialog">
            <header class="chapter-modal__header">
                <div class="chapter-modal__heading">
                    <i class="fa-solid fa-book-open"></i>
                    <div>
                        <h2 id="chapter-modal-title">Tạo lớp mới</h2>
                    </div>
                </div>
                <button type="button" class="chapter-modal__close">&times;</button>
            </header>

            <form class="chapter-form" action="#" method="post">
                <div class="chapter-form__body">
                    <div class="chapter-form__field">
                        <label>Chọn biểu tượng lớp</label>
                        <div class="chapter-icon-picker">
                            <?php foreach (['📊', '🔢', '📐', '📈', '🔺', '✖️', '📝', '📁', '🔬', '🌍', '💡', '🎯', '⭐', '🏆', '📋', '✏️'] as $index => $icon): ?>
                                <button type="button" class="chapter-icon-picker__item <?= $index === 0 ? 'is-active' : ''; ?>" data-icon="<?= $icon; ?>"><?= $icon; ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="icon" id="chapter-icon-input" value="📊">
                    </div>

                    <div class="chapter-preview">
                        <span class="chapter-preview__icon" id="chapter-preview-icon">📊</span>
                        <div>
                            <strong id="chapter-preview-title">Tên lớp...</strong>
                            <p id="chapter-preview-description">Môn học...</p>
                        </div>
                    </div>

                    <div class="chapter-form__field">
                        <label for="chapter-name">Tên lớp <span>*</span></label>
                        <input id="chapter-name" name="ten_lop" type="text" placeholder="VD: Lớp ABC..." required>
                    </div>
                    <div class="chapter-form__field">
                        <label for="chapter-description">Tên môn học</label>
                        <textarea id="chapter-description" name="ten_mon_hoc" placeholder="Tên môn học..."></textarea>
                    </div>
                </div>
                <footer class="chapter-modal__footer">
                    <button type="button" class="chapter-modal__cancel">Huỷ</button>
                    <button type="submit" class="chapter-modal__submit" disabled>
                        <i class="fa-solid fa-floppy-disk"></i> Tạo lớp
                    </button>
                </footer>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('chapter-modal');
            const modalTitle = document.getElementById('chapter-modal-title');
            const openModalButton = document.querySelector('.classes-header__btn');
            const closeModalButton = modal?.querySelector('.chapter-modal__close');
            const cancelModalButton = modal?.querySelector('.chapter-modal__cancel');
            const submitBtn = modal?.querySelector('.chapter-modal__submit');
            const chapterForm = document.querySelector('.chapter-form');

            const inputName = document.getElementById('chapter-name');
            const inputDesc = document.getElementById('chapter-description');
            const iconInput = document.getElementById('chapter-icon-input');
            const previewTitle = document.getElementById('chapter-preview-title');
            const previewDesc = document.getElementById('chapter-preview-description');
            const previewIcon = document.getElementById('chapter-preview-icon');
            const iconButtons = document.querySelectorAll('.chapter-icon-picker__item');

            const searchInput = document.getElementById('searchInput');
            const classCards = document.querySelectorAll('.class-card');

            searchInput?.addEventListener('input', function(e) {
                const keyword = e.target.value.toLowerCase().trim();

                classCards.forEach(card => {

                    const className = card.querySelector('.class-card__name')?.textContent.toLowerCase() || '';
                    const classCode = card.querySelector('.class-card__menu-code strong')?.textContent.toLowerCase() || '';
                    const subjectDesc = card.querySelector('.class-card__menu-btn')?.dataset.desc.toLowerCase() || '';

                    if (className.includes(keyword) || classCode.includes(keyword) || subjectDesc.includes(keyword)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            chapterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const isEdit = chapterForm.dataset.mode === 'edit';
                const classId = chapterForm.dataset.classId;
                const formData = new FormData(chapterForm);

                const url = isEdit ?
                    `index.php?page=teacher&action=many-class&api=update-class&id=${classId}` :
                    `index.php?page=teacher&action=many-class&api=create-class`;

                fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(isEdit ? 'Cập nhật lớp thành công!' : 'Tạo lớp thành công!');
                            window.location.reload();
                        } else {
                            alert('Có lỗi xảy ra: ' + (data.message || 'Không thể lưu.'));
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi parse JSON:', err);
                        alert('Lỗi kết nối máy chủ.');
                    });
            });

            const closeModal = () => {
                if (!modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            };

            if (openModalButton && modal) {
                openModalButton.addEventListener('click', () => {
                    modalTitle.textContent = 'Tạo lớp mới';
                    submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Tạo lớp';
                    chapterForm.reset();
                    delete chapterForm.dataset.mode;
                    delete chapterForm.dataset.classId;

                    previewIcon.textContent = '📊';
                    previewTitle.textContent = 'Tên lớp...';
                    previewDesc.textContent = 'Môn học...';

                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    inputName?.focus();
                });
            }

            closeModalButton?.addEventListener('click', closeModal);
            cancelModalButton?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            iconButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    iconButtons.forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');
                    const icon = this.dataset.icon;
                    if (iconInput) iconInput.value = icon;
                    if (previewIcon) previewIcon.textContent = icon;
                });
            });

            inputName?.addEventListener('input', function() {
                const val = this.value.trim();
                if (previewTitle) previewTitle.textContent = val || 'Tên lớp...';
                if (submitBtn) submitBtn.disabled = val.length === 0;
            });
            inputDesc?.addEventListener('input', function() {
                if (previewDesc) previewDesc.textContent = this.value.trim() || 'Môn học...';
            });

            function closeAllMenus() {
                document.querySelectorAll('.class-card__menu.is-open').forEach(m => m.classList.remove('is-open'));
            }

            document.querySelectorAll('.class-card__menu-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const menu = btn.nextElementSibling;
                    const isOpen = menu.classList.contains('is-open');
                    closeAllMenus();
                    if (!isOpen) menu.classList.add('is-open');
                });
            });
            document.addEventListener('click', () => closeAllMenus());

            document.querySelectorAll('.class-card__menu-item--edit').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const btn = item.closest('.class-card__menu-wrapper').querySelector('.class-card__menu-btn');
                    const {
                        id,
                        name,
                        desc,
                        icon
                    } = btn.dataset;

                    modalTitle.textContent = 'Sửa lớp học';
                    submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi';
                    chapterForm.dataset.mode = 'edit';
                    chapterForm.dataset.classId = id;

                    inputName.value = name || '';
                    inputDesc.value = desc || '';
                    if (iconInput) iconInput.value = icon || '📊';
                    if (previewIcon) previewIcon.textContent = icon || '📊';
                    if (previewTitle) previewTitle.textContent = name || 'Tên lớp...';
                    if (previewDesc) previewDesc.textContent = desc || 'Môn học...';

                    iconButtons.forEach(i => i.classList.toggle('is-active', i.dataset.icon === icon));

                    submitBtn.disabled = false;
                    modal.setAttribute('aria-hidden', 'false');
                    modal.classList.add('is-open');
                    closeAllMenus();
                });
            });

            document.querySelectorAll('.class-card__menu-item--delete').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    closeAllMenus();
                    const btn = item.closest('.class-card__menu-wrapper').querySelector('.class-card__menu-btn');
                    const {
                        id,
                        name
                    } = btn.dataset;

                    if (confirm(`Bạn có chắc muốn xóa lớp "${name}" không? Hành động này không thể hoàn tác.`)) {
                        fetch(`index.php?page=teacher&action=many-class&api=delete-class&id=${id}`, {
                                method: 'POST'
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    btn.closest('.class-card').remove();
                                } else {
                                    alert('Xóa lớp học thất bại, vui lòng thử lại.');
                                }
                            })
                            .catch(err => alert('Lỗi kết nối máy chủ khi xóa.'));
                    }
                });
            });
        })();
    </script>
    <?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
</body>

</html>