<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($examTitle ?? 'Bài thi'); ?> | Ascent</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/student/take-exam.css">
    <style>
        .result-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .result-card {
            background: #ffffff;
            padding: 36px 30px;
            border-radius: 18px;
            text-align: center;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
            animation: popUp 0.3s ease-out;
        }

        @keyframes popUp {
            from {
                transform: scale(0.85);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .result-icon {
            font-size: 54px;
            margin-bottom: 12px;
        }

        .result-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .result-score-box {
            margin: 20px 0;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .score-number {
            font-size: 40px;
            font-weight: 800;
            color: #2563eb;
        }

        .score-sub {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        .btn-return-class {
            display: inline-block;
            width: 100%;
            padding: 12px 0;
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }

        .btn-return-class:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

    <header>
        <div class="header-left">
            <button class="back-btn" title="Quay lại" onclick="history.back()">←</button>
            <span class="exam-title"><?php echo htmlspecialchars($examTitle ?? 'Bài thi'); ?></span>
        </div>
        <div class="header-right">
            <div class="timer" id="timer">⏱ --:--</div>
            <button class="submit-btn" onclick="submitExam()">Nộp bài</button>
        </div>
    </header>

    <main>
        <section class="question-card">
            <div class="question-count" id="questionCount"></div>
            <div class="question-text" id="questionText"></div>
            <div class="options" id="optionsList"></div>
            <div class="nav-buttons">
                <button class="nav-btn" id="prevBtn" onclick="goPrev()">← Câu trước</button>
                <button class="nav-btn primary" id="nextBtn" onclick="goNext()">Câu tiếp theo →</button>
            </div>
        </section>

        <aside class="sidebar">
            <h3>Danh sách câu hỏi</h3>
            <div class="question-grid" id="questionGrid"></div>
            <div class="legend">
                <div class="legend-item"><span class="dot answered"></span> Đã trả lời</div>
                <div class="legend-item"><span class="dot current"></span> Đang làm</div>
                <div class="legend-item"><span class="dot unanswered"></span> Chưa làm</div>
            </div>
        </aside>
    </main>
    <div class="result-modal-backdrop" id="resultModal">
        <div class="result-card">
            <div class="result-icon">🎉</div>
            <div class="result-title">Hoàn thành bài thi!</div>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Bạn đã hoàn thành lượt làm bài thi này.</p>

            <div class="result-score-box">
                <div class="score-number" id="resScore">0.0</div>
                <div class="score-sub" id="resDetail">Số câu đúng: 0 / 0</div>
            </div>

            <a id="btnBackToClass" class="btn-return-class" href="index.php?page=student&action=class&class_id=<?= (int)$classId ?>">>Quay về lớp học</a>
        </div>
    </div>

    <script>
        const classId = "<?php echo (int)($_GET['class_id'] ?? 0); ?>";
        document.getElementById("btnBackToClass").href = `index.php?page=student&action=class&class_id=${classId}`;
        const questions = <?php echo $questionsJson ?? '[]'; ?>;
        let secondsLeft = <?php echo $durationInSeconds ?? 0; ?>;
        let currentIndex = 0;

        const userAnswers = <?php echo $initialAnswersJson ?? '[]'; ?>;
        if (userAnswers.length < questions.length) {
            for (let i = userAnswers.length; i < questions.length; i++) {
                userAnswers.push(null);
            }
        }

        const letters = ["A", "B", "C", "D"];

        function renderQuestion() {
            if (questions.length === 0) return;

            const q = questions[currentIndex];
            const qCountEl = document.getElementById("questionCount");
            const qTextEl = document.getElementById("questionText");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");

            if (qCountEl) qCountEl.textContent = `Câu hỏi ${currentIndex + 1} / ${questions.length}`;
            if (qTextEl) qTextEl.textContent = q.text;

            const optionsList = document.getElementById("optionsList");
            if (optionsList) {
                optionsList.innerHTML = "";
                q.options.forEach((opt, i) => {
                    const selected = userAnswers[currentIndex] === i;
                    const label = document.createElement("label");
                    label.className = "option" + (selected ? " selected" : "");
                    label.innerHTML = `
                        <input type="radio" name="answer" value="${i}" ${selected ? "checked" : ""}>
                        <span class="option-letter">${letters[i]}.</span>
                        <span class="option-text">${opt}</span>
                    `;
                    const inputRadio = label.querySelector("input");
                    inputRadio.addEventListener("change", () => selectAnswer(i));

                    optionsList.appendChild(label);
                });
            }

            if (prevBtn) prevBtn.disabled = currentIndex === 0;
            if (nextBtn) {
                nextBtn.textContent = currentIndex === questions.length - 1 ? "Hoàn thành" : "Câu tiếp theo →";
            }

            renderGrid();
        }

        function selectAnswer(i) {
            userAnswers[currentIndex] = i;
            renderQuestion();

            const currentQ = questions[currentIndex];
            const selectedOptionId = currentQ.option_ids ? currentQ.option_ids[i] : null;
            if (selectedOptionId) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=save_answer&question_id=${currentQ.id}&option_id=${selectedOptionId}`
                });
            }
        }

        function renderGrid() {
            const grid = document.getElementById("questionGrid");
            if (!grid) return;
            grid.innerHTML = "";

            questions.forEach((_, i) => {
                const btn = document.createElement("button");
                btn.className = "q-btn";
                if (userAnswers[i] !== null) btn.classList.add("answered");
                if (i === currentIndex) btn.classList.add("current");
                btn.textContent = i + 1;
                btn.addEventListener("click", () => {
                    currentIndex = i;
                    renderQuestion();
                });
                grid.appendChild(btn);
            });
        }

        window.goPrev = function() {
            if (currentIndex > 0) {
                currentIndex--;
                renderQuestion();
            }
        };

        window.goNext = function() {
            if (currentIndex < questions.length - 1) {
                currentIndex++;
                renderQuestion();
            } else {
                submitExam(false);
            }
        };
        window.submitExam = function(force = false) {
            if (!force) {
                const answeredCount = userAnswers.filter(a => a !== null).length;
                const confirmed = confirm(
                    `Bạn đã trả lời ${answeredCount}/${questions.length} câu.\nBạn có chắc chắn muốn nộp bài không?`
                );
                if (!confirmed) return;
            }
            clearInterval(timerInterval);
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=submit_exam'
                })
                .then(res => res.text())
                .then(text => {
                    const jsonStart = text.indexOf('{');
                    const jsonEnd = text.lastIndexOf('}');

                    if (jsonStart === -1 || jsonEnd === -1) {
                        console.error("Dữ liệu server trả về không có JSON:", text);
                        throw new Error("Không tìm thấy JSON");
                    }

                    const res = JSON.parse(text.substring(jsonStart, jsonEnd + 1));

                    if (res.success) {
                        const data = res.data;
                        document.getElementById("resScore").textContent = `${data.score} / 10`;
                        document.getElementById("resDetail").textContent = `Làm đúng: ${data.correctAnswers} / ${data.totalQuestions} câu`;

                        const classId = "<?php echo (int)($_GET['class_id'] ?? 0); ?>";
                        document.getElementById("btnBackToClass").href = `index.php?page=student&action=class&class_id=${classId}`;

                        document.getElementById("resultModal").style.display = "flex";
                    } else {
                        alert("Có lỗi khi chấm điểm!");
                    }
                })
                .catch(err => {
                    console.error("Chi tiết lỗi:", err);
                    alert("Lỗi hiển thị kết quả! Nhấn F12 -> Console để xem dữ liệu trả về.");
                });
        };

        const timerEl = document.getElementById("timer");

        function updateTimer() {
            const min = Math.floor(secondsLeft / 60);
            const sec = secondsLeft % 60;

            if (timerEl) {
                timerEl.textContent = `⏱ ${String(min).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
                if (secondsLeft <= 60) timerEl.classList.add("warning");
            }

            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                alert("Hết giờ làm bài! Hệ thống đang tự động nộp bài thi của bạn.");
                window.submitExam(true);
                return;
            }
            secondsLeft--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        renderQuestion();
    </script>

</body>

</html>