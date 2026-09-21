<?php
function getClassOverview($classId)
{
    global $db;
    $sql = "
        SELECT 
            c.id AS class_id,
            c.name AS class_name,
            c.icon,
            COUNT(DISTINCT cs.user_id) AS total_students,
            COUNT(DISTINCT ch.id) AS total_chapters,
            COUNT(DISTINCT l.id) AS total_lessons,
            (SELECT COUNT(*) FROM exams WHERE class_id = c.id) AS total_exams,
            COALESCE(
                (
                    SELECT ch_sub.name
                    FROM chapter ch_sub
                    LEFT JOIN lessons l_sub ON ch_sub.id = l_sub.chapter_id
                    LEFT JOIN lesson_progress lp_sub ON l_sub.id = lp_sub.lesson_id AND lp_sub.is_completed = 1
                    WHERE ch_sub.class_id = c.id
                    GROUP BY ch_sub.id
                    ORDER BY (COUNT(lp_sub.user_id) / NULLIF(COUNT(DISTINCT l_sub.id), 0)) ASC, ch_sub.order_index ASC
                    LIMIT 1
                ), 
                'Chưa có'
            ) AS current_progress_chapter
        FROM classes c
        LEFT JOIN class_students cs ON c.id = cs.class_id
        LEFT JOIN chapter ch ON c.id = ch.class_id
        LEFT JOIN lessons l ON ch.id = l.chapter_id
        WHERE c.id = ? 
        GROUP BY c.id
    ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getRecentExams($classId)
{
    global $db;
    $sql = "
        SELECT 
            e.id AS exam_id,
            e.name AS exam_name,
            e.title AS exam_title,
            DATE_FORMAT(e.end_time, '%d/%m/%Y') AS end_date_formatted,
            COUNT(DISTINCT CASE 
                WHEN ea.status IN ('submitted', 'graded') THEN ea.user_id 
                ELSE NULL 
            END) AS completed_students,
            class_info.total_students,
            ROUND(
                (COUNT(DISTINCT CASE WHEN ea.status IN ('submitted', 'graded') THEN ea.user_id END) / 
                NULLIF(class_info.total_students, 0)) * 100
            , 0) AS completion_percentage
        FROM exams e
        LEFT JOIN (
            SELECT class_id, COUNT(user_id) AS total_students
            FROM class_students
            WHERE class_id = ?
            GROUP BY class_id
        ) class_info ON e.class_id = class_info.class_id
        LEFT JOIN exam_attempts ea ON e.id = ea.exam_id 
            AND ea.user_id IN (SELECT user_id FROM class_students WHERE class_id = ?)
        WHERE e.class_id = ?
        GROUP BY e.id, class_info.total_students
        ORDER BY e.created_at DESC
        LIMIT 2
    ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $classId, $classId, $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getRecentChapters($classId)
{
    global $db;
    $sql = "
        SELECT 
            ch.id AS chapter_id,
            ch.name AS chapter_name,
            ch.title AS chapter_title,
            COUNT(DISTINCT l.id) AS total_lessons,
            ROUND(
                COALESCE(COUNT(lp.lesson_id), 0) / NULLIF(class_info.total_students, 0)
            , 0) AS completed_lessons_avg,
            ROUND(
                COALESCE(
                    (COUNT(lp.lesson_id) / NULLIF(COUNT(DISTINCT l.id) * class_info.total_students, 0)) * 100, 
                    0
                )
            , 0) AS progress_percentage
        FROM chapter ch
        LEFT JOIN (
            SELECT class_id, COUNT(user_id) AS total_students
            FROM class_students
            WHERE class_id = ?
            GROUP BY class_id
        ) class_info ON ch.class_id = class_info.class_id
        LEFT JOIN lessons l ON ch.id = l.chapter_id
        LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id 
            AND lp.is_completed = 1
            AND lp.user_id IN (SELECT user_id FROM class_students WHERE class_id = ?)
        WHERE ch.class_id = ?
        GROUP BY ch.id, ch.order_index, class_info.total_students
        ORDER BY ch.order_index ASC, ch.created_at ASC
        LIMIT 2
    ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $classId, $classId, $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createNewChapter($classId, $name, $title, $teacherId)
{
    global $db;
    $stmtUser = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmtUser->bind_param("i", $teacherId);
    $stmtUser->execute();
    if ($stmtUser->get_result()->num_rows === 0) {
        throw new Exception("Tài khoản của bạn (ID = $teacherId) không tồn tại hoặc đã bị xóa khỏi Database. Vui lòng đăng xuất và đăng nhập lại!");
    }
    $stmtUser->close();

    $stmtOrder = $db->prepare("SELECT MAX(order_index) FROM chapter WHERE class_id = ?");
    $stmtOrder->bind_param("i", $classId);
    $stmtOrder->execute();
    $maxOrder = $stmtOrder->get_result()->fetch_row()[0] ?? 0;
    $newOrder = $maxOrder + 1;

    $sql = "INSERT INTO chapter (class_id, name, title, order_index, created_by) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("issii", $classId, $name, $title, $newOrder, $teacherId);
    return $stmt->execute();
}

function addStudentToClass($classId, $email)
{
    global $db;
    $stmtUser = $db->prepare("SELECT id FROM users WHERE email = ? AND role = 'student' AND status = 'active'");
    $stmtUser->bind_param("s", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser->num_rows === 0) {
        return ['success' => false, 'message' => 'Tài khoản học sinh không tồn tại hoặc đã bị khóa.'];
    }

    $studentId = $resultUser->fetch_assoc()['id'];
    $stmtInsert = $db->prepare("INSERT INTO class_students (class_id, user_id) VALUES (?, ?)");
    $stmtInsert->bind_param("ii", $classId, $studentId);

    try {
        if ($stmtInsert->execute()) {
            return ['success' => true];
        }
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()];
    }

    return ['success' => false, 'message' => 'Lỗi không xác định khi thêm học sinh.'];
}
