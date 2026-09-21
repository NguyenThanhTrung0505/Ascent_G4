<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đề thi | Ascent Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin/exam-management.css">
</head>

<body>

    <header>
        <a href="index.php" class="logo">
            <span><img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt=""></span>
            Ascent Admin
        </a>
        <nav>
            <a href="index.php?page=admin&action=dashboard">Trang chủ</a>
            <a href="index.php?page=admin&action=users">Quản lý người dùng</a>
            <a href="index.php?page=admin&action=class">Quản lý lớp</a>
            <a href="index.php?page=admin&action=exam" class="active">Quản lý đề thi</a>
            <a href="index.php?page=auth&action=logout">Đăng xuất</a>
        </nav>
        <div class="user-box">
            <div class="avatar">AA</div>
            <div class="user-info">
                <div class="user-name">Admin Ascent</div>
                <div class="user-role">Quản trị viên</div>
            </div>
        </div>
    </header>
    <div class="page-wrap">
        <h1 class="page-title">Quản lý đề thi</h1>
        <p class="page-sub">Hệ thống danh sách và tình trạng các đề thi kiểm tra</p>

        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-box">
                    🔍 <input type="text" id="searchInput" placeholder="Tìm kiếm nhanh...">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="published">Đang diễn ra</option>
                    <option value="closed">Đã kết thúc</option>
                    <option value="upcoming">Chưa bắt đầu</option>
                </select>
            </div>
            <button class="add-btn" onclick="openModal()">+ Tạo đề thi mới</button>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên đề thi</th>
                        <th>Môn học</th>
                        <th>Lớp học</th>
                        <th>Người tạo</th>
                        <th>Số câu hỏi</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
            <div id="emptyState" class="empty-state" style="display:none;">
                <div class="icon">🔍</div>
                <p><strong>Không tìm thấy đề thi nào</strong></p>
                <p style="font-size:13.5px; margin-top:4px;">Thử từ khóa khác hoặc bỏ bớt bộ lọc.</p>
            </div>
            <div class="table-footer">
                <span id="resultCount"></span>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <h3>Tạo đề thi mới</h3>
            <form id="addForm" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="editExamId" value="">
                <div class="form-group">
                    <label>Tên đề thi</label>
                    <input type="text" id="inputName" placeholder="VD: Kiểm tra 15p - Chương 2" required>
                    <div class="form-error" id="errorName">Vui lòng nhập tên đề thi.</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Môn học</label>
                        <input type="text" id="inputSubject" placeholder="VD: Toán học" required>
                        <div class="form-error" id="errorSubject">Vui lòng nhập môn học.</div>
                    </div>
                    <div class="form-group">
                        <label>Lớp học</label>
                        <select id="inputClass" required>
                            <option value="">-- Chọn lớp học --</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $cls): ?>
                                    <option value="<?= $cls['class_id'] ?>">
                                        <?= htmlspecialchars($cls['class_display_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-error" id="errorClass">Vui lòng nhập lớp học.</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số câu hỏi</label>
                        <input type="number" id="inputQuestionCount" placeholder="VD: 20" min="1" required>
                        <div class="form-error" id="errorQuestionCount">Số câu phải lớn hơn 0.</div>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select id="inputStatus">
                            <option value="notstarted">Chưa bắt đầu</option>
                            <option value="ongoing">Đang diễn ra</option>
                            <option value="ended">Đã kết thúc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn-confirm">Tạo đề thi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="detailOverlay">
        <div class="modal-box">
            <h3 id="detailTitle">Chi tiết đề thi</h3>
            <div id="detailContent"></div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDetail()">Đóng</button>
            </div>
        </div>
    </div>

    <script>
        const statusLabels = {
            published: {
                text: "Đang diễn ra",
                class: "ongoing"
            },
            closed: {
                text: "Đã kết thúc",
                class: "ended"
            },
            upcoming: {
                text: "Chưa bắt đầu",
                class: "notstarted"
            }
        };

        let exams = <?php echo $examsJson ?? '[]'; ?>;

        const PAGE_SIZE = 10;
        let currentPage = 1;

        function renderTable() {
            const searchInput = document.getElementById("searchInput");
            if (!searchInput) return;

            const keyword = searchInput.value.trim().toLowerCase();
            const statusValue = document.getElementById("statusFilter").value;

            const filtered = exams.filter(e => {
                const nameToSearch = e.exam_name || e.name || "";
                const creatorToSearch = e.creator_name || e.creator || "";

                const matchKeyword = nameToSearch.toLowerCase().includes(keyword) ||
                    creatorToSearch.toLowerCase().includes(keyword);
                const matchStatus = statusValue === "all" || e.status === statusValue;
                return matchKeyword && matchStatus;
            });

            const totalRows = filtered.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * PAGE_SIZE;
            const pageRows = filtered.slice(startIndex, startIndex + PAGE_SIZE);

            const tableBody = document.getElementById("tableBody");
            const emptyState = document.getElementById("emptyState");

            if (totalRows === 0) {
                tableBody.innerHTML = "";
                emptyState.style.display = "block";
            } else {
                emptyState.style.display = "none";
                tableBody.innerHTML = pageRows.map((e, i) => {
                    const st = statusLabels[e.status] || statusLabels['upcoming'];

                    const examId = e.exam_id || e.id;
                    const examName = e.exam_name || e.name;
                    const className = e.class_name || e.classroom;
                    const subjectName = e.subject_name || e.subject;
                    const creator = e.creator_name || e.creator;
                    const qCount = e.total_questions || e.count || 0;

                    return `
        <tr>
          <td>${startIndex + i + 1}</td>
          <td><a class="exam-name-link" onclick="showDetail(${exams.indexOf(e)})">${examName}</a></td>
          <td>${subjectName}</td>
          <td>${className}</td>
          <td>${creator}</td>
          <td>${qCount} câu</td>
          <td><span class="badge ${st.class}">${st.text}</span></td>
          <td>
            <div class="action-icons">
              <button class="edit" title="Sửa" onclick="alert('Chức năng sửa chờ làm tiếp.')">✏️</button>
              <button class="delete" title="Xóa" onclick="handleDelete(${exams.indexOf(e)})">🗑️</button>
            </div>
          </td>
        </tr>
      `;
                }).join("");
            }

            const fromCount = totalRows === 0 ? 0 : startIndex + 1;
            const toCount = Math.min(startIndex + PAGE_SIZE, totalRows);
            document.getElementById("resultCount").textContent =
                `Hiển thị ${fromCount} - ${toCount} trên tổng số ${totalRows} dòng`;

            const paginationEl = document.getElementById("pagination");
            if (totalPages <= 1) {
                paginationEl.innerHTML = "";
            } else {
                let buttonsHtml = "";
                for (let p = 1; p <= totalPages; p++) {
                    buttonsHtml += `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
                }
                paginationEl.innerHTML = buttonsHtml;
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderTable();
        }

        async function handleDelete(index) {
            const exam = exams[index];
            const examName = exam.exam_name || exam.name;
            const examId = exam.exam_id || exam.id;

            if (confirm(`Bạn có chắc muốn xóa đề thi "${examName}" không?`)) {
                try {
                    const response = await fetch('index.php?page=admin&action=exam', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'delete',
                            id: examId
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        exams.splice(index, 1);
                        renderTable();
                    } else {
                        alert("Lỗi khi xóa: " + (result.error || "Không xác định"));
                    }
                } catch (error) {
                    console.error("Lỗi:", error);
                    alert("Không thể kết nối đến máy chủ.");
                }
            }
        }

        function showDetail(index) {
            const e = exams[index];
            const st = statusLabels[e.status] || statusLabels['upcoming'];

            document.getElementById("detailTitle").textContent = e.exam_name || e.name;
            document.getElementById("detailContent").innerHTML = `
      <div class="detail-row"><span class="detail-label">Môn học</span><span class="detail-value">${e.subject_name || e.subject}</span></div>
      <div class="detail-row"><span class="detail-label">Lớp học</span><span class="detail-value">${e.class_name || e.classroom}</span></div>
      <div class="detail-row"><span class="detail-label">Người tạo</span><span class="detail-value">${e.creator_name || e.creator}</span></div>
      <div class="detail-row"><span class="detail-label">Số câu hỏi</span><span class="detail-value">${e.total_questions || e.count || 0} câu</span></div>
      <div class="detail-row"><span class="detail-label">Thời gian làm bài</span><span class="detail-value">${e.duration || 0} phút</span></div>
      <div class="detail-row"><span class="detail-label">Trạng thái</span><span class="badge ${st.class}">${st.text}</span></div>
    `;
            document.getElementById("detailOverlay").classList.add("show");
        }

        function closeDetail() {
            document.getElementById("detailOverlay").classList.remove("show");
        }

        function openModal() {
            document.getElementById("modalOverlay").classList.add("show");
        }

        function closeModal() {
            document.getElementById("modalOverlay").classList.remove("show");
            document.getElementById("addForm").reset();
            ["errorName", "errorClass"].forEach(id => {
                if (document.getElementById(id)) document.getElementById(id).style.display = "none";
            });
        }

        async function handleSubmit(event) {
            event.preventDefault();
            const name = document.getElementById("inputName").value.trim();
            const class_id = document.getElementById("inputClass").value;
            const status = document.getElementById("inputStatus").value;
            const duration = document.getElementById("inputDuration") ? document.getElementById("inputDuration").value : 45;
            let valid = true;
            if (name === "") {
                document.getElementById("errorName").style.display = "block";
                valid = false;
            }
            if (class_id === "") {
                document.getElementById("errorClass").style.display = "block";
                valid = false;
            }

            if (!valid) return false;

            const payload = {
                action: 'add',
                name: name,
                class_id: class_id,
                duration: duration,
                status: status,
                title: ''
            };

            try {
                const response = await fetch('index.php?page=admin&action=exam', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert("Lỗi khi thêm: " + (result.error || "Không xác định"));
                }
            } catch (error) {
                console.error("Lỗi:", error);
                alert("Không thể lưu đề thi.");
            }
        }

        const searchInputObj = document.getElementById("searchInput");
        const statusFilterObj = document.getElementById("statusFilter");

        if (searchInputObj) searchInputObj.addEventListener("input", renderTable);
        if (statusFilterObj) statusFilterObj.addEventListener("change", renderTable);

        renderTable();
    </script>

</body>

</html>