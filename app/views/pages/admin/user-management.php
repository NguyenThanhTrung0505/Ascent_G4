<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng | Ascent Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin/user-management.css">
</head>

<body>

    <header>
        <a href="index.php" class="logo">
            <span><img src="<?= BASE_URL ?>/assets/images/header/logo.png" alt=""></span>
            Ascent Admin
        </a>
        <nav>
            <a href="index.php?page=admin&action=dashboard">Trang chủ</a>
            <a href="index.php?page=admin&action=users" class="active">Quản lý người dùng</a>
            <a href="index.php?page=admin&action=class">Quản lý lớp</a>
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
        <h1 class="page-title">Quản lý người dùng</h1>
        <p class="page-sub">Danh sách tài khoản giáo viên và học sinh trên nền tảng</p>

        <div class="tabs">
            <button class="tab-btn active" data-tab="teacher" onclick="switchTab('teacher')">Giáo viên</button>
            <button class="tab-btn" data-tab="student" onclick="switchTab('student')">Học sinh</button>
        </div>

        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-box">
                    🔍 <input type="text" id="searchInput" placeholder="Tìm kiếm nhanh...">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="active">Hoạt động</option>
                    <option value="locked">Tạm khóa</option>
                </select>
            </div>
            <button class="add-btn" id="addBtn" onclick="openModal()">+ Thêm giáo viên</button>
        </div>

        <div class="table-card">
            <table>
                <thead id="tableHead"></thead>
                <tbody id="tableBody"></tbody>
            </table>
            <div id="emptyState" class="empty-state" style="display:none;">
                <div class="icon">🔍</div>
                <p><strong>Không tìm thấy kết quả nào</strong></p>
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
            <h3 id="modalTitle">Thêm giáo viên mới</h3>
            <form id="addForm" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="inputId" value="">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" id="inputName" required>
                    <div class="form-error" id="errorName">Vui lòng nhập họ tên.</div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="inputEmail" required>
                    <div class="form-error" id="errorEmail">Email không đúng định dạng.</div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn-confirm">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const data = <?php echo $dataJson ?? '{}'; ?>;
        let currentTab = "teacher";
        const PAGE_SIZE = 10;
        let currentPage = 1;

        function switchTab(tab) {
            if (!data[tab]) return;
            currentTab = tab;
            document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.toggle("active", btn.dataset.tab === tab));
            const addBtn = document.getElementById("addBtn");
            if (addBtn) addBtn.textContent = data[tab].addLabel;
            document.getElementById("searchInput").value = "";
            document.getElementById("statusFilter").value = "all";
            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            if (!data[currentTab] || !data[currentTab].rows) return;
            const tab = data[currentTab];
            document.getElementById("tableHead").innerHTML = `<tr>${tab.columns.map(c => `<th>${c}</th>`).join("")}</tr>`;
            const keyword = document.getElementById("searchInput").value.trim().toLowerCase();
            const statusValue = document.getElementById("statusFilter").value;
            let filteredRows = tab.rows.filter(r => {
                const matchKeyword = r.name.toLowerCase().includes(keyword) || r.email.toLowerCase().includes(keyword);
                const matchStatus = statusValue === "all" || r.status === statusValue;
                return matchKeyword && matchStatus;
            });

            const totalRows = filteredRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * PAGE_SIZE;
            const pageRows = filteredRows.slice(startIndex, startIndex + PAGE_SIZE);

            const tableBody = document.getElementById("tableBody");
            if (totalRows === 0) {
                tableBody.innerHTML = "";
                document.getElementById("emptyState").style.display = "block";
            } else {
                document.getElementById("emptyState").style.display = "none";
                tableBody.innerHTML = pageRows.map((r, i) => `
                <tr>
                    <td>${startIndex + i + 1}</td>
                    <td>${r.name}</td>
                    <td>${r.email}</td>
                    <td>${r.extra}</td>
                    <td>
                        <span class="badge ${r.status === 'active' ? 'active' : 'locked'}" style="cursor:pointer;" 
                              onclick="toggleStatus(${r.id}, '${r.status}')" title="Nhấp để đổi trạng thái">
                              ${r.status === 'active' ? 'Hoạt động' : 'Tạm khóa'}
                        </span>
                    </td>
                    <td>
                        <div class="action-icons">
                            <button class="edit" title="Sửa" onclick="openModal('edit', ${r.id}, '${r.name}', '${r.email}')">✏️</button>
                            <button class="delete" title="Xóa" onclick="handleDelete(${r.id}, '${r.name}')">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join("");
            }
            document.getElementById("resultCount").textContent = `Hiển thị ${totalRows === 0 ? 0 : startIndex + 1} - ${Math.min(startIndex + PAGE_SIZE, totalRows)} trên tổng ${totalRows}`;
            let buttonsHtml = "";
            for (let p = 1; p <= totalPages; p++) {
                buttonsHtml += `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
            }
            document.getElementById("pagination").innerHTML = buttonsHtml;
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
            if (confirm(`Hành động này không thể hoàn tác.\nBạn có chắc muốn xóa "${name}" không?`)) {
                apiCall({
                    action: 'delete',
                    id: id,
                    role: currentTab
                });
            }
        }

        function toggleStatus(id, currentStatus) {
            if (confirm(`Bạn muốn đổi trạng thái tài khoản này?`)) {
                apiCall({
                    action: 'toggle_status',
                    id: id,
                    current_status: currentStatus
                });
            }
        }

        function openModal(mode = 'add', id = '', name = '', email = '') {
            document.getElementById("modalOverlay").classList.add("show");

            if (mode === 'edit') {
                document.getElementById("modalTitle").textContent = "Sửa thông tin";
                document.getElementById("inputId").value = id;
                document.getElementById("inputName").value = name;
                document.getElementById("inputEmail").value = email;
                document.getElementById("btnSubmit").textContent = "Cập nhật";
            } else {
                document.getElementById("modalTitle").textContent = data[currentTab].modalTitle;
                document.getElementById("inputId").value = "";
                document.getElementById("inputName").value = "";
                document.getElementById("inputEmail").value = "";
                document.getElementById("btnSubmit").textContent = "Thêm mới";
            }
        }

        function closeModal() {
            document.getElementById("modalOverlay").classList.remove("show");
            document.getElementById("addForm").reset();
        }

        function handleSubmit(event) {
            event.preventDefault();
            const id = document.getElementById("inputId").value;
            const name = document.getElementById("inputName").value.trim();
            const email = document.getElementById("inputEmail").value.trim();

            if (id) {
                apiCall({
                    action: 'edit',
                    id: id,
                    name: name,
                    email: email
                });
            } else {
                apiCall({
                    action: 'add',
                    name: name,
                    email: email,
                    role: currentTab
                });
            }
        }
        document.getElementById("searchInput").addEventListener("input", renderTable);
        document.getElementById("statusFilter").addEventListener("change", renderTable);
        if (Object.keys(data).length > 0) renderTable();
    </script>

</body>

</html>