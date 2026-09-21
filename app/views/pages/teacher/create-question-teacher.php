<?php
$class_id = (int) ($_GET['class_id'] ?? 1);
$class_names = [];
$class_name = $class_names[$class_id] ?? 'Toán 6C';
$chapters = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">

</head>

<body>
    <main class="create-question-page">
        <div class="create-question-topbar">
            <a class="create-question-back" href="javascript:history.back()">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Quay lại
            </a>
            <div class="create-question-heading">
                <h1>Tạo Đề Thi Mới <span aria-hidden="true">✨</span></h1>
                <div class="create-question-actions">
                    <button type="button" class="create-question-action create-question-action--preview">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i> Xem trước
                    </button>
                    <button type="button" class="create-question-action create-question-action--draft">
                        Lưu nháp
                    </button>
                    <button type="button" class="create-question-action create-question-action--publish" disabled>
                        <i class="fa-regular fa-paper-plane" aria-hidden="true"></i> Xuất bản
                    </button>
                </div>
            </div>
        </div>

        <div class="create-question-layout">
            <section class="create-question-content">
                <div class="question-card-list">
                    <article class="question-card" data-question-number="1">
                        <header class="question-card__header">
                            <span class="question-card__drag" aria-hidden="true">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </span>
                            <span class="question-card__number">1</span>
                            <div class="question-card__heading">
                                <span class="question-card__title">Nhập câu hỏi...</span>
                                <span class="question-card__summary">0/4 đáp án • Đúng: A</span>
                            </div>
                            <i class="fa-solid fa-circle-exclamation question-card__warning" aria-hidden="true"></i>
                            <button type="button" class="question-card__collapse" aria-label="Thu gọn câu hỏi">
                                <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
                            </button>
                        </header>

                        <div class="question-card__body">
                            <div class="create-question-field">
                                <label>Câu hỏi <span>*</span></label>
                                <textarea class="question-content"
                                    placeholder="Nhập nội dung câu hỏi tại đây..."></textarea>
                            </div>

                            <div class="create-question-field">
                                <label>Đáp án (chọn đáp án đúng)</label>
                                <div class="answer-list">
                                    <?php foreach (['A', 'B', 'C', 'D'] as $index => $letter): ?>
                                        <div class="answer-row <?= $index === 0 ? 'answer-row--correct' : ''; ?>">
                                            <button type="button" class="answer-row__badge"
                                                aria-label="Chọn đáp án <?= $letter; ?>">
                                                <?= $letter; ?>
                                            </button>
                                            <input type="text" placeholder="Đáp án <?= $letter; ?>...">
                                            <button type="button" class="answer-row__remove"
                                                aria-label="Xóa đáp án <?= $letter; ?>">
                                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="answer-add">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Thêm đáp án
                                </button>
                            </div>

                            <div class="create-question-field">
                                <label for="question-explanation">Giải thích đáp án <small>(tuỳ chọn)</small></label>
                                <textarea id="question-explanation"
                                    placeholder="Giải thích tại sao đáp án này đúng để học sinh hiểu hơn..."></textarea>
                            </div>
                        </div>
                    </article>
                </div>
                <button type="button" class="add-question-button">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Thêm câu hỏi mới
                </button>
            </section>

            <aside class="create-question-sidebar">
                <section class="question-settings-card">
                    <h2><i class="fa-regular fa-clipboard" aria-hidden="true"></i> Thông tin đề</h2>
                    <div class="create-question-field">
                        <label for="exam-name">Tên đề <span>*</span></label>
                        <input id="exam-name" type="text" placeholder="VD: Kiểm tra Phân số...">
                    </div>
                    <!-- <div class="create-question-field">
                        <label for="exam-class">Lớp học</label>
                        <select id="exam-class">
                            <option selected></option>
                            <option>Toán 6A</option>
                            <option>Sinh 6B</option>
                        </select>

                    </div> -->
                </section>

                <section class="question-settings-card">
                    <h2 class="question-settings-card__title--teal">
                        <i class="fa-solid fa-gear" aria-hidden="true"></i> Cài đặt
                    </h2>
                    <div class="create-question-field">
                        <label for="exam-duration">Thời gian làm bài (phút) <span>*</span></label>
                        <input id="exam-duration" type="number" min="0" placeholder="VD: 120">
                        <small>Nhập 0 để không giới hạn thời gian</small>
                    </div>
                    <div class="create-question-field">
                        <label for="exam-attempts">Số lượt làm</label>
                        <input id="exam-attempts" type="number" min="0" placeholder="VD: 120">
                        <small>Nhập 0 để không giới hạn lượt làm</small>
                    </div>
                    <div class="create-question-field">
                        <label>Thời gian giao đề <span>*</span></label>
                        <div class="question-date-field">
                            <input id="exam-start" type="datetime-local" aria-label="Thời gian bắt đầu">
                            <span>Từ</span>
                        </div>
                        <div class="question-date-field">
                            <input id="exam-end" type="datetime-local" aria-label="Thời gian kết thúc">
                            <span>Đến</span>
                        </div>
                        <small class="question-date-error" id="exam-date-error" role="alert"></small>
                    </div>
                </section>
                <section class="question-summary-card">
                    <h2>Tóm tắt đề</h2>
                    <dl>
                        <div>
                            <dt>Tổng câu hỏi</dt>
                            <dd id="summary-question-count">1</dd>
                        </div>
                        <div>
                            <dt>Đã chọn đáp án</dt>
                            <dd id="summary-answer-count">0/1</dd>
                        </div>
                        <div>
                            <dt>Thời gian làm bài</dt>
                            <dd id="summary-duration">0 phút</dd>
                        </div>
                        <div>
                            <dt>Ngày bắt đầu</dt>
                            <dd id="summary-start-date">Chưa chọn</dd>
                        </div>
                        <div>
                            <dt>Ngày kết thúc</dt>
                            <dd id="summary-end-date">Chưa chọn</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </main>
    <script>
        (() => {
            const questionList = document.querySelector('.question-card-list');
            const addQuestionButton = document.querySelector('.add-question-button');
            const publishButton = document.querySelector('.create-question-action--publish');
            const draftButton = document.querySelector('.create-question-action--draft');
            const examName = document.getElementById('exam-name');
            const durationInput = document.getElementById('exam-duration');
            const startInput = document.getElementById('exam-start');
            const endInput = document.getElementById('exam-end');
            const dateError = document.getElementById('exam-date-error');
            const summaryQuestionCount = document.getElementById('summary-question-count');
            const summaryAnswerCount = document.getElementById('summary-answer-count');
            const summaryDuration = document.getElementById('summary-duration');
            const summaryStartDate = document.getElementById('summary-start-date');
            const summaryEndDate = document.getElementById('summary-end-date');
            const maxAnswers = 6;

            const letters = (count) => Array.from({
                length: count
            }, (_, index) => String.fromCharCode(65 + index));
            const createAnswerRows = (count = 4) => letters(count).map((letter, index) => `
                <div class="answer-row ${index === 0 ? 'answer-row--correct' : ''}">
                    <button type="button" class="answer-row__badge" aria-label="Chọn đáp án ${letter}">${letter}</button>
                    <input type="text" placeholder="Đáp án ${letter}...">
                    <button type="button" class="answer-row__remove" aria-label="Xóa đáp án ${letter}">
                        <i class="fa-regular fa-trash-can" aria-hidden="true"></i>
                    </button>
                </div>`).join('');
            const createQuestionTemplate = (number) => `
                <article class="question-card" data-question-number="${number}">
                    <header class="question-card__header">
                        <span class="question-card__drag" aria-hidden="true"><i class="fa-solid fa-grip-vertical"></i></span>
                        <span class="question-card__number">${number}</span>
                        <div class="question-card__heading">
                            <span class="question-card__title">Nhập câu hỏi...</span>
                            <span class="question-card__summary">0/4 đáp án • Đúng: A</span>
                        </div>
                        <i class="fa-solid fa-circle-exclamation question-card__warning" aria-hidden="true"></i>
                        <button type="button" class="question-card__collapse" aria-label="Thu gọn câu hỏi"><i class="fa-solid fa-chevron-up" aria-hidden="true"></i></button>
                    </header>
                    <div class="question-card__body">
                        <div class="create-question-field"><label>Câu hỏi <span>*</span></label><textarea class="question-content" placeholder="Nhập nội dung câu hỏi tại đây..."></textarea></div>
                        <div class="create-question-field"><label>Đáp án (chọn đáp án đúng)</label><div class="answer-list">${createAnswerRows()}</div><button type="button" class="answer-add"><i class="fa-solid fa-plus" aria-hidden="true"></i> Thêm đáp án</button></div>
                        <div class="create-question-field"><label>Giải thích đáp án <small>(tuỳ chọn)</small></label><textarea placeholder="Giải thích tại sao đáp án này đúng để học sinh hiểu hơn..."></textarea></div>
                    </div>
                </article>`;

            const isQuestionValid = (card) => {
                const rows = [...card.querySelectorAll('.answer-row')];
                const correct = card.querySelector('.answer-row--correct');
                return Boolean(card.querySelector('.question-content').value.trim()) &&
                    rows.filter((row) => row.querySelector('input').value.trim()).length >= 2 &&
                    Boolean(correct && correct.querySelector('input').value.trim());
            };

            const hasAnsweredQuestion = (card) => {
                const correct = card.querySelector('.answer-row--correct input');
                return Boolean(correct && correct.value.trim());
            };

            const formatDate = (value) => {
                if (!value) return 'Chưa chọn';
                const [date, time] = value.split('T');
                const [year, month, day] = date.split('-');
                return `${day}/${month}/${year} ${time}`;
            };

            const updateQuestion = (card) => {
                const rows = [...card.querySelectorAll('.answer-row')];
                const correct = card.querySelector('.answer-row--correct');
                const filled = rows.filter((row) => row.querySelector('input').value.trim()).length;
                const correctLetter = correct?.querySelector('.answer-row__badge')?.textContent.trim() || '-';
                const title = card.querySelector('.question-content').value.trim();
                card.querySelector('.question-card__title').textContent = title || 'Nhập câu hỏi...';
                card.querySelector('.question-card__summary').textContent = `${filled}/${rows.length} đáp án • Đúng: ${correctLetter}`;
                const warning = card.querySelector('.question-card__warning');
                const valid = isQuestionValid(card);
                warning.className = `fa-solid ${valid ? 'fa-circle-check question-card__warning--valid' : 'fa-circle-exclamation'} question-card__warning`;
                warning.setAttribute('aria-label', valid ? 'Câu hỏi hợp lệ' : 'Câu hỏi chưa hoàn tất');
                rows.forEach((row, index) => {
                    const letter = String.fromCharCode(65 + index);
                    row.querySelector('.answer-row__badge').textContent = letter;
                    row.querySelector('.answer-row__badge').setAttribute('aria-label', `Chọn đáp án ${letter}`);
                    row.querySelector('.answer-row__remove').setAttribute('aria-label', `Xóa đáp án ${letter}`);
                });
                const removeButtons = card.querySelectorAll('.answer-row__remove');
                removeButtons.forEach((button) => {
                    button.disabled = rows.length <= 2;
                });
                const addButton = card.querySelector('.answer-add');
                addButton.disabled = rows.length >= maxAnswers;
                return valid;
            };

            const updateDateValidation = () => {
                const invalid = startInput.value && endInput.value && new Date(endInput.value) <= new Date(startInput.value);
                endInput.classList.toggle('input--invalid', Boolean(invalid));
                dateError.textContent = invalid ? 'Thời gian kết thúc phải sau thời gian bắt đầu.' : '';
                return !invalid;
            };

            const updateSummary = () => {
                const cards = [...questionList.querySelectorAll('.question-card')];
                cards.forEach(updateQuestion);
                const answered = cards.filter(hasAnsweredQuestion).length;
                summaryQuestionCount.textContent = cards.length;
                summaryAnswerCount.textContent = `${answered}/${cards.length}`;
                summaryDuration.textContent = durationInput.value === '' ? 'Chưa chọn' : (Number(durationInput.value) === 0 ? 'Không giới hạn' : `${durationInput.value} phút`);
                summaryStartDate.textContent = formatDate(startInput.value);
                summaryEndDate.textContent = formatDate(endInput.value);
                updateDateValidation();
                const valid = Boolean(examName.value.trim()) && durationInput.value !== '' && Number(durationInput.value) >= 0 &&
                    Boolean(startInput.value && endInput.value) && updateDateValidation() && cards.length > 0 && cards.every(isQuestionValid);
                publishButton.disabled = !valid;
            };

            const collapseCard = (card, collapsed) => {
                card.classList.toggle('question-card--collapsed', collapsed);
                card.querySelector('.question-card__collapse i').className = `fa-solid fa-chevron-${collapsed ? 'down' : 'up'}`;
            };

            addQuestionButton.addEventListener('click', () => {
                questionList.querySelectorAll('.question-card').forEach((card) => collapseCard(card, true));
                const number = questionList.children.length + 1;
                questionList.insertAdjacentHTML('beforeend', createQuestionTemplate(number));
                const newCard = questionList.lastElementChild;
                updateSummary();
                newCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                newCard.querySelector('.question-content').focus();
            });

            questionList.addEventListener('click', (event) => {
                const card = event.target.closest('.question-card');
                if (!card) return;
                const addAnswer = event.target.closest('.answer-add');
                const badge = event.target.closest('.answer-row__badge');
                const remove = event.target.closest('.answer-row__remove');
                const collapse = event.target.closest('.question-card__collapse');
                const header = event.target.closest('.question-card__header');
                if (addAnswer && card.querySelectorAll('.answer-row').length < maxAnswers) {
                    const answerList = card.querySelector('.answer-list');
                    const letter = String.fromCharCode(65 + answerList.children.length);
                    answerList.insertAdjacentHTML('beforeend', `<div class="answer-row"><button type="button" class="answer-row__badge" aria-label="Chọn đáp án ${letter}">${letter}</button><input type="text" placeholder="Đáp án ${letter}..."><button type="button" class="answer-row__remove" aria-label="Xóa đáp án ${letter}"><i class="fa-regular fa-trash-can" aria-hidden="true"></i></button></div>`);
                } else if (badge) {
                    card.querySelectorAll('.answer-row').forEach((row) => row.classList.remove('answer-row--correct'));
                    badge.closest('.answer-row').classList.add('answer-row--correct');
                } else if (remove && card.querySelectorAll('.answer-row').length > 2) {
                    const row = remove.closest('.answer-row');
                    row.remove();
                    if (!card.querySelector('.answer-row--correct')) card.querySelector('.answer-row').classList.add('answer-row--correct');
                } else if (collapse || header) {
                    collapseCard(card, !card.classList.contains('question-card--collapsed'));
                }
                updateSummary();
            });

            questionList.addEventListener('input', (event) => {
                if (event.target.closest('.question-card')) updateSummary();
            });
            [examName, durationInput, startInput, endInput].forEach((input) => {
                input.addEventListener('input', updateSummary);
                input.addEventListener('change', updateSummary);
            });
            draftButton.addEventListener('click', () => {
                const toast = document.createElement('div');
                toast.className = 'create-question-toast';
                toast.textContent = `Đã lưu nháp lúc ${new Date().toLocaleTimeString('vi-VN')}`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3200);
            });
            publishButton.addEventListener('click', async () => {
                const originalText = publishButton.innerHTML;
                publishButton.disabled = true;
                publishButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Đang xử lý...';

                try {
                    const payload = {
                        exam: {
                            name: examName.value.trim(),
                            class_id: 1,
                            duration: Number(durationInput.value) || 0,
                            attempts: Number(document.getElementById('exam-attempts').value) || 0,
                            start_time: startInput.value,
                            end_time: endInput.value
                        },
                        questions: []
                    };

                    const cards = document.querySelectorAll('.question-card');
                    cards.forEach(card => {
                        const content = card.querySelector('.question-content').value.trim();
                        const textareas = card.querySelectorAll('textarea');
                        const explanation = textareas.length > 1 ? textareas[1].value.trim() : '';

                        const options = [];
                        card.querySelectorAll('.answer-row').forEach(row => {
                            options.push({
                                content: row.querySelector('input').value.trim(),
                                is_correct: row.classList.contains('answer-row--correct') ? 1 : 0
                            });
                        });

                        payload.questions.push({
                            content,
                            explanation,
                            options
                        });
                    });

                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        alert('Tuyệt vời! ' + result.message);
                        window.location.href = 'index.php?page=teacher&action=class-detail&class_id=<?= $classId ?>';
                    } else {
                        alert(result.message);
                        publishButton.disabled = false;
                        publishButton.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error(error);
                    alert('Đã xảy ra lỗi kết nối. Vui lòng thử lại!');
                    publishButton.disabled = false;
                    publishButton.innerHTML = originalText;
                }
            });
            updateSummary();
        })();
    </script>

</body>

</html>