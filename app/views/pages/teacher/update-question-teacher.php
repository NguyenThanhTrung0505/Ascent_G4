<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật Đề thi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/teacher/style-teacher.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
</head>

<body>
    <main class="create-question-page">
        <div class="create-question-topbar">
            <a class="create-question-back" href="javascript:history.back()">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
            <div class="create-question-heading">
                <h1>Cập nhật Đề Thi <span aria-hidden="true">✏️</span></h1>
                <div class="create-question-actions">
                    <button type="button" class="create-question-action create-question-action--preview">
                        <i class="fa-regular fa-eye"></i> Xem trước
                    </button>
                    <button type="button" class="create-question-action create-question-action--publish" disabled>
                        <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
        <div class="create-question-layout">
            <section class="create-question-content">
                <div class="question-card-list">
                </div>
                <button type="button" class="add-question-button">
                    <i class="fa-solid fa-plus"></i> Thêm câu hỏi mới
                </button>
            </section>

            <aside class="create-question-sidebar">
                <section class="question-settings-card">
                    <h2><i class="fa-regular fa-clipboard"></i> Thông tin đề</h2>
                    <div class="create-question-field">
                        <label for="exam-name">Tên đề <span>*</span></label>
                        <input id="exam-name" type="text" placeholder="VD: Kiểm tra Phân số...">
                    </div>
                    <div class="create-question-field">
                        <label for="exam-class">Lớp học</label>
                        <select id="exam-class">
                            <option value="1" selected><?= $class_name; ?></option>
                        </select>
                    </div>
                    <div class="create-question-field">
                        <label for="exam-chapter">Chương học</label>
                        <select id="exam-chapter">
                            <option value="">Trống</option>
                            <?php foreach ($chapters as $chapter): ?>
                                <option><?= $chapter; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </section>

                <section class="question-settings-card">
                    <h2 class="question-settings-card__title--teal"><i class="fa-solid fa-gear"></i> Cài đặt</h2>
                    <div class="create-question-field">
                        <label for="exam-duration">Thời gian (phút) <span>*</span></label>
                        <input id="exam-duration" type="number" min="0">
                    </div>
                    <div class="create-question-field">
                        <label for="exam-attempts">Số lượt làm</label>
                        <input id="exam-attempts" type="number" min="0">
                    </div>
                    <div class="create-question-field">
                        <label>Thời gian giao đề <span>*</span></label>
                        <div class="question-date-field"><input id="exam-start" type="datetime-local"><span>Từ</span></div>
                        <div class="question-date-field"><input id="exam-end" type="datetime-local"><span>Đến</span></div>
                        <small class="question-date-error" id="exam-date-error" role="alert"></small>
                    </div>
                </section>

                <section class="question-summary-card">
                    <!-- Box Tóm tắt (Giữ nguyên HTML của bạn) -->
                    <h2>Tóm tắt đề</h2>
                    <dl>
                        <div>
                            <dt>Tổng câu hỏi</dt>
                            <dd id="summary-question-count">0</dd>
                        </div>
                        <div>
                            <dt>Đã chọn đáp án</dt>
                            <dd id="summary-answer-count">0/0</dd>
                        </div>
                        <div>
                            <dt>Thời gian</dt>
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
            const existingData = <?= $examJsonData ?>;

            const questionList = document.querySelector('.question-card-list');
            const addQuestionButton = document.querySelector('.add-question-button');
            const publishButton = document.querySelector('.create-question-action--publish');
            const examName = document.getElementById('exam-name');
            const durationInput = document.getElementById('exam-duration');
            const startInput = document.getElementById('exam-start');
            const endInput = document.getElementById('exam-end');
            const attemptsInput = document.getElementById('exam-attempts');

            const maxAnswers = 6;
            const letters = (count) => Array.from({
                length: count
            }, (_, i) => String.fromCharCode(65 + i));

            const createAnswerRows = (options) => {
                if (!options || options.length === 0) {
                    options = [{
                        content: '',
                        is_correct: 1
                    }, {
                        content: '',
                        is_correct: 0
                    }, {
                        content: '',
                        is_correct: 0
                    }, {
                        content: '',
                        is_correct: 0
                    }];
                }
                return options.map((opt, index) => {
                    const letter = String.fromCharCode(65 + index);
                    const isCorrect = (opt.is_correct == 1 || opt.is_correct === true);
                    const safeContent = opt.content ? opt.content.replace(/"/g, '&quot;') : '';

                    return `
                    <div class="answer-row ${isCorrect ? 'answer-row--correct' : ''}">
                        <button type="button" class="answer-row__badge" aria-label="Chọn đáp án ${letter}">${letter}</button>
                        <input type="text" placeholder="Đáp án ${letter}..." value="${safeContent}">
                        <button type="button" class="answer-row__remove" aria-label="Xóa"><i class="fa-regular fa-trash-can"></i></button>
                    </div>`;
                }).join('');
            };

            const createQuestionTemplate = (number, qData = null) => {
                const content = qData ? qData.content : '';
                const explanation = (qData && qData.explanation) ? qData.explanation : '';
                return `
                    <article class="question-card" data-question-number="${number}">
                        <header class="question-card__header">
                            <span class="question-card__drag"><i class="fa-solid fa-grip-vertical"></i></span>
                            <span class="question-card__number">${number}</span>
                            <div class="question-card__heading">
                                <span class="question-card__title">${content || 'Nhập câu hỏi...'}</span>
                                <span class="question-card__summary">...</span>
                            </div>
                            <i class="fa-solid fa-circle-exclamation question-card__warning"></i>
                            <button type="button" class="question-card__collapse"><i class="fa-solid fa-chevron-up"></i></button>
                        </header>
                        <div class="question-card__body">
                            <div class="create-question-field">
                                <label>Câu hỏi <span>*</span></label>
                                <textarea class="question-content" placeholder="Nội dung...">${content}</textarea>
                            </div>
                            <div class="create-question-field">
                                <label>Đáp án</label>
                                <div class="answer-list">${createAnswerRows(qData ? qData.options : null)}</div>
                                <button type="button" class="answer-add"><i class="fa-solid fa-plus"></i> Thêm đáp án</button>
                            </div>
                            <div class="create-question-field">
                                <label>Giải thích</label>
                                <textarea placeholder="Giải thích...">${explanation}</textarea>
                            </div>
                        </div>
                    </article>`;
            };

            const formatForInput = (dbDate) => dbDate ? dbDate.replace(' ', 'T').slice(0, 16) : '';
            examName.value = existingData.name;
            durationInput.value = existingData.duration;
            attemptsInput.value = existingData.attempts || 0;
            startInput.value = formatForInput(existingData.start_time);
            endInput.value = formatForInput(existingData.end_time);

            questionList.innerHTML = '';
            existingData.questions.forEach((q, index) => {
                questionList.insertAdjacentHTML('beforeend', createQuestionTemplate(index + 1, q));
            });

            const isQuestionValid = (card) => {
                const rows = [...card.querySelectorAll('.answer-row')];
                const correct = card.querySelector('.answer-row--correct');
                return Boolean(card.querySelector('.question-content').value.trim()) &&
                    rows.filter(row => row.querySelector('input').value.trim()).length >= 2 &&
                    Boolean(correct && correct.querySelector('input').value.trim());
            };

            const hasAnsweredQuestion = (card) => {
                const correct = card.querySelector('.answer-row--correct input');
                return Boolean(correct && correct.value.trim());
            };

            const updateQuestion = (card) => {
                const rows = [...card.querySelectorAll('.answer-row')];
                const correct = card.querySelector('.answer-row--correct');
                const filled = rows.filter(row => row.querySelector('input').value.trim()).length;
                const correctLetter = correct?.querySelector('.answer-row__badge')?.textContent.trim() || '-';
                const title = card.querySelector('.question-content').value.trim();

                card.querySelector('.question-card__title').textContent = title || 'Nhập câu hỏi...';
                card.querySelector('.question-card__summary').textContent = `${filled}/${rows.length} đáp án • Đúng: ${correctLetter}`;

                const valid = isQuestionValid(card);
                const warning = card.querySelector('.question-card__warning');
                warning.className = `fa-solid ${valid ? 'fa-circle-check question-card__warning--valid' : 'fa-circle-exclamation'} question-card__warning`;

                rows.forEach((row, index) => {
                    const letter = String.fromCharCode(65 + index);
                    row.querySelector('.answer-row__badge').textContent = letter;
                });

                card.querySelectorAll('.answer-row__remove').forEach(btn => btn.disabled = rows.length <= 2);
                card.querySelector('.answer-add').disabled = rows.length >= maxAnswers;
                return valid;
            };

            const updateSummary = () => {
                const cards = [...questionList.querySelectorAll('.question-card')];
                cards.forEach(updateQuestion);

                const answered = cards.filter(hasAnsweredQuestion).length;
                document.getElementById('summary-question-count').textContent = cards.length;
                document.getElementById('summary-answer-count').textContent = `${answered}/${cards.length}`;

                const valid = Boolean(examName.value.trim()) && durationInput.value !== '' &&
                    startInput.value && endInput.value && cards.length > 0 && cards.every(isQuestionValid);
                publishButton.disabled = !valid;
            };

            addQuestionButton.addEventListener('click', () => {
                questionList.insertAdjacentHTML('beforeend', createQuestionTemplate(questionList.children.length + 1));
                updateSummary();
            });

            questionList.addEventListener('click', (event) => {
                const card = event.target.closest('.question-card');
                if (!card) return;

                if (event.target.closest('.answer-add') && card.querySelectorAll('.answer-row').length < maxAnswers) {
                    const letter = String.fromCharCode(65 + card.querySelectorAll('.answer-row').length);
                    card.querySelector('.answer-list').insertAdjacentHTML('beforeend',
                        `<div class="answer-row"><button type="button" class="answer-row__badge">${letter}</button><input type="text" placeholder="Đáp án ${letter}..."><button type="button" class="answer-row__remove"><i class="fa-regular fa-trash-can"></i></button></div>`
                    );
                } else if (event.target.closest('.answer-row__badge')) {
                    card.querySelectorAll('.answer-row').forEach(row => row.classList.remove('answer-row--correct'));
                    event.target.closest('.answer-row').classList.add('answer-row--correct');
                } else if (event.target.closest('.answer-row__remove') && card.querySelectorAll('.answer-row').length > 2) {
                    event.target.closest('.answer-row').remove();
                    if (!card.querySelector('.answer-row--correct')) card.querySelector('.answer-row').classList.add('answer-row--correct');
                } else if (event.target.closest('.question-card__collapse') || event.target.closest('.question-card__header')) {
                    const collapsed = !card.classList.contains('question-card--collapsed');
                    card.classList.toggle('question-card--collapsed', collapsed);
                    card.querySelector('.question-card__collapse i').className = `fa-solid fa-chevron-${collapsed ? 'down' : 'up'}`;
                }
                updateSummary();
            });

            questionList.addEventListener('input', updateSummary);
            [examName, durationInput, startInput, endInput, attemptsInput].forEach(input => input.addEventListener('input', updateSummary));

            updateSummary();

            publishButton.addEventListener('click', async () => {
                publishButton.disabled = true;
                publishButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

                try {
                    const payload = {
                        exam: {
                            name: examName.value.trim(),
                            class_id: 1,
                            duration: Number(durationInput.value) || 0,
                            attempts: Number(attemptsInput.value) || 0,
                            start_time: startInput.value,
                            end_time: endInput.value
                        },
                        questions: []
                    };

                    document.querySelectorAll('.question-card').forEach(card => {
                        const textareas = card.querySelectorAll('textarea');
                        const options = [...card.querySelectorAll('.answer-row')].map(row => ({
                            content: row.querySelector('input').value.trim(),
                            is_correct: row.classList.contains('answer-row--correct') ? 1 : 0
                        }));

                        payload.questions.push({
                            content: textareas[0].value.trim(),
                            explanation: textareas.length > 1 ? textareas[1].value.trim() : '',
                            options: options
                        });
                    });

                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    if (result.status === 'success') {
                        alert(result.message);
                        window.location.href = 'index.php?page=teacher&action=class-detail&class_id=<?= $classId ?>';
                    } else {
                        alert(result.message);
                        publishButton.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi';
                        publishButton.disabled = false;
                    }
                } catch (error) {
                    alert('Lỗi kết nối máy chủ!');
                    publishButton.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi';
                    publishButton.disabled = false;
                }
            });
        })();
    </script>
</body>

</html>