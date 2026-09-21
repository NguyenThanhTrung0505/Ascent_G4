<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lớp học | Ascent Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin/class-management.css">
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
            <a href="index.php?page=admin&action=class" class="active">Quản lý lớp</a>
            <a href="index.php?page=admin&action=exam">Quản lý đề thi</a>
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
        <h1 class="page-title">Quản lý lớp học</h1>
        <p class="page-sub">Theo dõi và thiết lập phân phối các lớp học hiện tại</p>

        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-box">
                    🔍 <input type="text" id="searchInput" placeholder="Tìm kiếm nhanh...">
                </div>
                <select class="filter-select" id="subjectFilter">
                    <option value="all">Tất cả môn học</option>
                </select>
            </div>
            <button class="add-btn" onclick="openModal()">+ Thêm lớp mới</button>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên lớp</th>
                        <th>Môn học</th>
                        <th>Giáo viên phụ trách</th>
                        <th>Sỹ số</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
            <div id="emptyState" class="empty-state" style="display:none;">
                <div class="icon">🔍</div>
                <p><strong>Không tìm thấy lớp học nào</strong></p>
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
            <h3 id="modalTitle">Thêm lớp học mới</h3>
            <form id="addForm" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="inputId" value="">

                <div class="form-row">
                    <div class="form-group">
                        <label>Mã lớp</label>
                        <input type="text" id="inputCode" placeholder="VD: TOAN6A" required>
                    </div>
                    <div class="form-group">
                        <label>Tên lớp</label>
                        <input type="text" id="inputName" placeholder="VD: Toán 6A" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Môn học</label>
                        <select id="inputSubject" required>
                            <option value="">-- Chọn môn học --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giáo viên phụ trách</label>
                        <select id="inputTeacher" required>
                            <option value="">-- Chọn giáo viên --</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn-confirm" id="btnSubmit">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="detailOverlay">
        <div class="modal-box">
            <h3 id="detailTitle">Chi tiết lớp học</h3>
            <div id="detailContent"></div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDetail()">Đóng</button>
            </div>
        </div>
    </div>
    <script>
        let classes = <?php echo $classesJson ?? '[]'; ?>;
        const subjects = <?php echo $subjectsJson ?? '[]'; ?>;
        const teachers = <?php echo $teachersJson ?? '[]'; ?>;

        const subjectFilter = document.getElementById("subjectFilter");
        const inputSubject = document.getElementById("inputSubject");
        subjects.forEach(s => {
            subjectFilter.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.name}</option>`);
            inputSubject.insertAdjacentHTML('beforeend', `<option value="${s.id}">${s.name}</option>`);
        });

        const inputTeacher = document.getElementById("inputTeacher");
        teachers.forEach(t => {
            inputTeacher.insertAdjacentHTML('beforeend', `<option value="${t.id}">${t.username} (${t.email})</option>`);
        });

        const PAGE_SIZE = 10;
        let currentPage = 1;

        function renderTable() {
            const keyword = document.getElementById("searchInput").value.trim().toLowerCase();
            const subjectValue = subjectFilter.value;

            const filtered = classes.filter(c => {
                const matchKeyword = c.name.toLowerCase().includes(keyword) ||
                    c.class_code.toLowerCase().includes(keyword) ||
                    c.teacher_name.toLowerCase().includes(keyword);
                const matchSubject = subjectValue === "all" || c.subject_id == subjectValue;
                return matchKeyword && matchSubject;
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
                tableBody.innerHTML = pageRows.map((c, i) => `
                    <tr>
                        <td>${startIndex + i + 1}</td>
                        <td><strong>${c.class_code}</strong></td>
                        <td><a class="class-name-link" style="cursor:pointer; color: #4361ee;" onclick="showDetail(${c.id})">${c.name}</a></td>
                        <td>${c.subject_name}</td>
                        <td>${c.teacher_name}</td>
                        <td>${c.size} học sinh</td>
                        <td>${c.date}</td>
                        <td>
                            <div class="action-icons">
                                <button class="edit" title="Sửa" onclick="openModal('edit', ${c.id})">✏️</button>
                                <button class="delete" title="Xóa" onclick="handleDelete(${c.id}, '${c.name}')">🗑️</button>
                            </div>
                        </td>
                    </tr>
                `).join("");
            }

            const fromCount = totalRows === 0 ? 0 : startIndex + 1;
            const toCount = Math.min(startIndex + PAGE_SIZE, totalRows);
            document.getElementById("resultCount").textContent = `Hiển thị ${fromCount} - ${toCount} trên tổng ${totalRows} dòng`;

            const paginationEl = document.getElementById("pagination");
            let buttonsHtml = "";
            for (let p = 1; p <= totalPages; p++) {
                buttonsHtml += `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
            }
            paginationEl.innerHTML = totalPages <= 1 ? "" : buttonsHtml;
        }

        function goToPage(page) {
            currentPage = page;
            renderTable();
        }

        async function apiCall(bodyData) {
            try {
                const res = await fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(bodyData)
                });
                const result = await res.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert("Lỗi: " + result.error);
                }
            } catch (e) {
                alert("Lỗi kết nối máy chủ");
            }
        }

        function handleDelete(id, name) {
            if (confirm(`Bạn có chắc muốn xóa lớp "${name}" không?\nLưu ý: Hành động này có thể xóa học sinh và bài học liên quan!`)) {
                apiCall({
                    action: 'delete',
                    id: id
                });
            }
        }

        function openModal(mode, classId = null) {
            document.getElementById("modalOverlay").classList.add("show");
            const form = document.getElementById("addForm");

            if (mode === 'edit') {
                const c = classes.find(item => item.id === classId);
                document.getElementById("modalTitle").textContent = "Sửa thông tin lớp";
                document.getElementById("inputId").value = c.id;
                document.getElementById("inputCode").value = c.class_code;
                document.getElementById("inputCode").disabled = true;
                document.getElementById("inputName").value = c.name;
                document.getElementById("inputSubject").value = c.subject_id;
                document.getElementById("inputTeacher").value = c.teacher_id;
                document.getElementById("btnSubmit").textContent = "Cập nhật";
            } else {
                form.reset();
                document.getElementById("modalTitle").textContent = "Thêm lớp học mới";
                document.getElementById("inputId").value = "";
                document.getElementById("inputCode").disabled = false;
                document.getElementById("btnSubmit").textContent = "Thêm mới";
            }
        }

        function closeModal() {
            document.getElementById("modalOverlay").classList.remove("show");
        }

        function handleSubmit(event) {
            event.preventDefault();
            const id = document.getElementById("inputId").value;
            const data = {
                class_code: document.getElementById("inputCode").value.trim(),
                name: document.getElementById("inputName").value.trim(),
                subject_id: document.getElementById("inputSubject").value,
                teacher_id: document.getElementById("inputTeacher").value
            };

            if (id) {
                data.action = 'edit';
                data.id = id;
            } else {
                data.action = 'add';
            }
            apiCall(data);
        }

        function showDetail(id) {
            const c = classes.find(item => item.id === id);
            document.getElementById("detailTitle").textContent = c.name;
            document.getElementById("detailContent").innerHTML = `
                <div class="detail-row"><span class="detail-label">Mã lớp</span><span class="detail-value">${c.class_code}</span></div>
                <div class="detail-row"><span class="detail-label">Môn học</span><span class="detail-value">${c.subject_name}</span></div>
                <div class="detail-row"><span class="detail-label">Giáo viên phụ trách</span><span class="detail-value">${c.teacher_name}</span></div>
                <div class="detail-row"><span class="detail-label">Sỹ số</span><span class="detail-value">${c.size} học sinh</span></div>
                <div class="detail-row"><span class="detail-label">Ngày tạo</span><span class="detail-value">${c.date}</span></div>
            `;
            document.getElementById("detailOverlay").classList.add("show");
        }

        function closeDetail() {
            document.getElementById("detailOverlay").classList.remove("show");
        }
        document.getElementById("searchInput").addEventListener("input", renderTable);
        subjectFilter.addEventListener("change", renderTable);

        renderTable();
    </script>

</body>

</html>