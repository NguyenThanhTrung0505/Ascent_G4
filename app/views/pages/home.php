<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascent</title>
    <!-- Font text -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Bubblegum+Sans&family=Jost:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet" />
    <!-- Css -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/home.css">
</head>

<body>
    <!-- HEADER -->
    <header id="header">
        <div class="header-top">
            <div class="container header-container">
                <div class="header-info">
                    <a href="">(629) 555-0129</a>
                    <a href="">info@example.com</a>
                    <p>6391 Đường A</p>
                </div>
                <div class="header-icon">
                    <ul>
                        <li>
                            <a href="">
                                <i></i>
                            </a>
                        </li>
                        <li>
                            <a href=""><i></i></a>
                        </li>
                        <li>
                            <a href=""><i></i></a>
                        </li>
                        <li>
                            <a href=""><i></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div
                class="container header-bottom-container">
                <a href="" class="header-brand">
                    <img
                        src="<?= BASE_URL ?>/assets/images/header/logo.png"
                        alt=""
                        width="40px"
                        height="40px" />
                    <p>Ascent</p>
                </a>
                <div class="header-nav">
                    <nav class="navbar">
                        <div class="container-fluid">
                            <div
                                class="navbar-collapse"
                                id="navbarScroll">
                                <ul
                                    class="navbar-nav">
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active"
                                            aria-current="page"
                                            href="#header">Trang chủ</a>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active"
                                            aria-current="page"
                                            href="#">Giáo viên</a>
                                        <ul class="nav-menu">
                                            <li>
                                                <a
                                                    href="#create-exam"
                                                    class="menu-item">
                                                    Soạn đề
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="#teacher-func"
                                                    class="menu-item">
                                                    Không gian làm việc
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active"
                                            aria-current="page"
                                            href="#">Học sinh</a>
                                        <ul class="nav-menu">
                                            <li>
                                                <a
                                                    href="#take-exam"
                                                    class="menu-item">

                                                    Bài kiểm tra
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="#student-func"
                                                    class="menu-item">
                                                    Tính năng
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active"
                                            aria-current="page"
                                            href="#team">Dành cho</a>
                                    </li>
                                </ul>
                                <a href="index.php?page=auth&action=login" class="btn btn-header">
                                    Đăng nhập
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- BANNER -->
    <section class="banner" id="banner">
        <div class="container">
            <div
                class="banner-img-circle">
                <img src="<?= BASE_URL ?>/assets/images/home/left-circle-1.png" alt="" />
                <img src="<?= BASE_URL ?>/assets/images/home/left-circle-2.png" alt="" />
            </div>
            <div
                class="banner-content">
                <div class="banner-content-left-pic">
                    <img src="<?= BASE_URL ?>/assets/images/home/boy_img_1.png" alt="" />
                </div>
                <div
                    class="banner-text">
                    <h1>
                        <img src="<?= BASE_URL ?>/assets/images/home/shap-1.png" alt="" />
                        <span>Giao Bài Tập Và Chấm Thi</span>
                        <br />
                        <span>Tự Động</span>
                        <span>Thông Minh</span>
                    </h1>
                    <p class="banner-text-bottom">
                        Hệ thống hỗ trợ giảng dạy hiện đại, giúp số hóa đề thi và
                        <br />
                        chấm điểm tự động nhanh chóng, chính xác
                    </p>
                    <a href="index.php?page=auth&action=register">Bắt đầu miễn phí</a>
                </div>
                <div
                    class="banner-content-three-start">
                    <img
                        src="<?= BASE_URL ?>/assets/images/home/shap.png"
                        alt=""
                        width="auto"
                        height="50px" />
                    <img
                        src="<?= BASE_URL ?>/assets/images/home/shap.png"
                        alt=""
                        width="auto"
                        height="60px" />
                    <img
                        src="<?= BASE_URL ?>/assets/images/home/shap.png"
                        alt=""
                        width="auto"
                        height="30px" />
                </div>
                <img
                    src="<?= BASE_URL ?>/assets/images/home/painting.png"
                    alt=""
                    width="auto"
                    height="100%" />
                <div class="banner-content-right-boy">
                    <img src="<?= BASE_URL ?>/assets/images/home/boy_img_2.png" alt="" />
                </div>
            </div>
            <div class="banner-right-circle">
                <img src="<?= BASE_URL ?>/assets/images/home/right-circle.png" alt="" />
            </div>
        </div>
    </section>
    <!-- CREATE-EXAM -->
    <section class="create-exam" id="create-exam">
        <div class="container">
            <div
                class="create-exam-content">
                <div class="left-content">
                    <p>Soạn đề thông minh</p>
                    <h2>
                        Thiết kế đề thi chuẩn xác <br />
                        chỉ trong vài phút
                    </h2>
                    <p>
                        Đơn giản hóa quy trình tạo đề trắc nghiệm với giao diện trực quan. Dễ dàng thiết lập thời gian làm bài, cấu hình số lượt thi, thêm lời giải thích chi tiết và phân loại theo từng lớp học, chương học một cách khoa học.
                    </p>
                    <button><a href="index.php?page=auth&action=register">Bắt đầu ngay</a></button>
                </div>
                <div
                    class="right-content">
                    <img src="<?= BASE_URL ?>/assets/images/home/CreateQuestion-teacher.png" alt="">
                </div>
            </div>
        </div>
    </section>
    <!-- TEACHER FUNC -->
    <section class="teacher-func" id="teacher-func">
        <div class="container">
            <div class="teacher-func-content">
                <div class="title">
                    <p class="header-title-p">
                        Không gian làm việc của giáo viên
                    </p>
                    <h2
                        class="header-title-h2">
                        Tối ưu hóa việc giảng dạy và <br />
                        quản lý lớp học
                    </h2>
                </div>
                <div class="tab">
                    <div class="tab-content" id="nav-tabContent">
                        <div
                            class="tab-pane fade show active"
                            id="nav-education">
                            <div>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/home/create-topic.png"
                                    style="object-fit: contain;"
                                    alt="" />
                                <div class="card-text">
                                    <a href="">Soạn bài đơn giản</a>
                                    <p style="text-align: center;">Tích hợp công cụ soạn thảo, upload tài liệu và tạo bài giảng nhanh chóng</p>
                                    <a href=""></a>
                                </div>
                            </div>
                            <div>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/home/teacher-list.png"
                                    alt="" />
                                <div class="card-text">
                                    <a href="">Quản lý dễ dàng</a>
                                    <p style="text-align: center;">Quản lý danh sách các chương, đề thi trực quan</p>
                                    <a href=""></a>
                                </div>
                            </div>
                            <div>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/home/ManyClass-teacher.png"
                                    alt="" />
                                <div class="card-text">
                                    <a href="">Quản lý đa lớp học</a>
                                    <p style="text-align: center;">Điều phối và sắp xếp lịch trình giảng dạy nhiều lớp học cùng lúc hiệu quả</p>
                                    <a href=""></a>
                                </div>
                            </div>
                            <div>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/home/Dashboard-teacher.png"
                                    alt="" />
                                <div class="card-text">
                                    <a href="">Bảng điều khiển</a>
                                    <p style="text-align: center;">Thao tác đơn giản, nhanh chóng hiện đại</p>
                                    <a href=""></a>
                                </div>
                            </div>
                            <div>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/home/Class-teacher.png"
                                    alt="" />
                                <div class="card-text">
                                    <a href="">Không gian lớp học</a>
                                    <p style="text-align: center;">Quản lý sĩ số, giao bài tập và tương tác lớp học</p>
                                    <a href=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- TAKE EXAM -->
    <section class="take-exam" id="take-exam">
        <div class="container">
            <div class="take-exam-content">
                <div class="content-left">
                    <div
                        class="img">
                        <img src="<?= BASE_URL ?>/assets/images/home/shap-1.png" alt="" />
                        <img src="<?= BASE_URL ?>/assets/images/home/take-exam-student.png" alt="" />
                    </div>
                </div>
                <div class="content-right">
                    <p class="header-title-p">
                        Kiểm tra trực tuyến
                    </p>
                    <h2
                        class="header-title-h2">
                        Đánh giá năng lực chính xác,
                        thao tác mượt mà
                    </h2>
                    <p
                        class="p" style="margin-top: 1rem;">
                        Môi trường làm bài thi hiện đại với thiết kế tinh gọn. Hệ thống tích hợp đồng hồ đếm ngược, danh sách điều hướng câu hỏi thông minh và tính năng nộp bài tự động, giúp tối ưu hóa quá trình kiểm tra.
                    </p>
                    <button><a href="index.php?page=auth&action=register">Bắt đầu ngay</a></button>
                    <img
                        src="<?= BASE_URL ?>/assets/images/home/pencil-rocket.png"
                        alt="" />
                </div>
            </div>
        </div>
    </section>
    <!-- STUDENT FUNC -->
    <section class="student-func" id="student-func">
        <div class="container">
            <div class="student-func-heading">
                <span class="header-title-p">Tính năng dành cho học sinh</span>

                <h2
                    class="header-title-h2">
                    Tối ưu hóa trải nghiệm học tập với hệ thống quản lý thông minh
                </h2>
            </div>

            <div class="student-cards">
                <!-- LEFT -->
                <div class="student-cards-left">
                    <div class="student-func-card student-func-card--horizontal">
                        <div class="student-func-card-image">
                            <img src="<?= BASE_URL ?>/assets/images/home/topic-student.png" alt="" />
                        </div>

                        <div class="student-func-card-content">
                            <h3 class="student-func-card-title">
                                Quản lý dễ dàng
                            </h3>
                            <div class="student-func-card-meta">
                                <span>Quản lý danh sách các chương, đề thi một cách trực quan và hiệu quả.</span>
                            </div>
                        </div>
                    </div>

                    <div class="student-func-card student-func-card--horizontal">
                        <div class="student-func-card-image">
                            <img src="<?= BASE_URL ?>/assets/images/home/class-student.png" alt="" />
                        </div>

                        <div class="student-func-card-content">

                            <h3 class="student-func-card-title">
                                Lộ trình học rõ ràng
                            </h3>
                            <div class="student-func-card-meta">
                                <span>Theo dõi tiến độ bài học và kết quả kiểm tra chi tiết theo từng môn học.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="student-cards-right">
                    <div
                        class="student-func-card student-func-card--horizontal">
                        <div class="student-func-card-image">
                            <img src="<?= BASE_URL ?>/assets/images/home/dashboard-student.png" alt="" />
                        </div>

                        <div class="student-func-card-content">
                            <h3 class="student-func-card-title">
                                Bảng điều khiển trung tâm
                            </h3>
                            <div class="student-func-card-meta">
                                <span>Tổng hợp mọi nhiệm vụ học tập, hạn nộp bài và đánh giá năng lực ngay trên một màn hình duy nhất, giúp học sinh chủ động thời gian.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- TEAM -->
    <section class="team" id="team">
        <div class="container">
            <div class="team-heading">
                <span class="team-tag">
                    Phù Hợp Cho Mọi Đối Tượng
                </span>
                <h2
                    class="team-title">
                    Nền Tảng Dạy & Học Tự Động <br> Cho Mọi Nhu Cầu
                </h2>
            </div>
            <div class="team-row">
                <div class="team-item">
                    <div class="team-img">
                        <img
                            src="<?= BASE_URL ?>/assets/images/home/actor_1.png"
                            alt="" />
                    </div>
                    <div class="team-content">
                        <h4 class="team-name">Giáo viên cá nhân</h4>
                        <p class="team-job" style="text-align: start; padding-left: 1rem">- Giao bài, chấm điểm tự động, theo dõi tiến độ lớp học. <br>
                            - Tiết kiệm thời gian giao bài và chấm bài.</p>
                        <div class="team-social">
                            <div class="team-social">
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <div class="team-img">
                        <img
                            src="<?= BASE_URL ?>/assets/images/home/actor_3.png"
                            class="img-fluid w-100"
                            alt="" />
                    </div>
                    <div class="team-content">
                        <h4 class="team-name">Trường học</h4>
                        <p class="team-job" style="text-align: start; padding-left: 1rem">- Tổ chức kiểm tra, thi thử đồng loạt, chấm điểm tự động. <br>
                            - Quản lý khóa học, giao bài theo chương.</p>
                        <div class="team-social">
                            <div class="team-social">
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <div class="team-img">
                        <img
                            src="<?= BASE_URL ?>/assets/images/home/actor_5.png"
                            class="img-fluid w-100"
                            alt="" />
                    </div>

                    <div class="team-content">
                        <h4 class="team-name">Doanh nghiệp</h4>
                        <p class="team-job" style="text-align: start; padding-left: 1rem">- Tạo bài kiểm tra, đánh giá kỹ năng nhân viên. <br>
                            - Xây dựng khoá học, gắn nội dung theo phòng ban.</p>
                        <div class="team-social">
                            <div class="team-social">
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                                <a href="#"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col brand-col">
                    <h3 class="footer-logo">
                        <img
                            src="<?= BASE_URL ?>/assets/images/header/logo.png"
                            alt=""
                            width="40px"
                            height="40px" /><span>Ascent</span>
                    </h3>
                    <p class="footer-desc">
                        Hệ thống giao bài tập và chấm thi tự động thông minh, tối ưu hóa thời gian và nâng cao chất lượng giảng dạy.
                    </p>
                    <div class="footer-socials">
                        <a href="#" class="social-link">FB</a>
                        <a href="#" class="social-link">YT</a>
                        <a href="#" class="social-link">IN</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4 class="footer-title">Khám Phá</h4>
                    <ul class="footer-links">
                        <li><a href="#header">Trang chủ</a></li>
                        <li><a href="#create-exam">Tính năng ra đề</a></li>
                        <li><a href="#teacher-func">Không gian làm việc</a></li>
                        <li><a href="#take-exam">Bài kiểm tra</a></li>
                        <li><a href="#student-func">Tính năng của học sinh</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-title">Giải Pháp</h4>
                    <ul class="footer-links">
                        <li><a href="#team">Dành cho học sinh</a></li>
                        <li><a href="#team">Dành cho giáo viên</a></li>
                        <li><a href="#team">Khối trường học</a></li>
                        <li><a href="#team">Doanh nghiệp</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-title">Liên Hệ</h4>
                    <div class="footer-contact">
                        <p>Email: <span>support@eduplatform.vn</span></p>
                        <p>Hotline: <span>1900 6868</span></p>
                        <p>Giờ làm việc: <span>08:00 - 18:00</span></p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Ascent. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
    <script src="<?= BASE_URL ?>/assets/js/home.js"></script>
</body>

</html>