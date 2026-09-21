<?php
function getBage($studentId, $classId)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT 
                c.id AS class_id,
                c.name AS class_name, 
                c.icon AS class_icon,
                u.username AS teacher_name,
                COUNT(DISTINCT ch.id) AS total_chapters,
                COUNT(DISTINCT l.id) AS total_lessons,
                COUNT(DISTINCT CASE WHEN lp.is_completed = 1 THEN lp.lesson_id END) AS completed_lessons,
                COALESCE(
                    ROUND(
                        COUNT(DISTINCT CASE WHEN lp.is_completed = 1 THEN lp.lesson_id END) * 100.0 / 
                        NULLIF(COUNT(DISTINCT l.id), 0), 
                        0
                    ), 
                    0
                ) AS overall_progress_rate,
                (
                    SELECT ROUND(AVG(best_score), 1)
                    FROM (
                        SELECT MAX(ea.score) AS best_score
                        FROM exam_attempts ea
                        JOIN exams e ON ea.exam_id = e.id
                        WHERE e.class_id = ?
                        AND ea.user_id = ?
                        AND ea.status = 'graded'
                        AND ea.score IS NOT NULL
                        GROUP BY ea.exam_id
                    ) AS student_exam_scores
                ) AS avg_score
            FROM classes c
            JOIN users u ON c.teacher_id = u.id
            LEFT JOIN chapter ch ON c.id = ch.class_id
            LEFT JOIN lessons l ON ch.id = l.chapter_id
            LEFT JOIN lesson_progress lp 
                ON l.id = lp.lesson_id 
                AND lp.user_id = ?
                AND lp.is_completed = 1
            WHERE c.id = ?
            GROUP BY c.id, c.name, c.icon, u.username;
        "
    );
    $stmt->bind_param("iiii", $classId, $studentId, $studentId, $classId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}
function getAllExam($studentId, $classId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            e.id AS exam_id,
            COALESCE(e.title, e.name) AS exam_title,
            ROUND(MAX(ea.score), 1) AS highest_score,
            10 AS max_score,
            CASE 
                WHEN COUNT(CASE WHEN ea.status IN ('submitted', 'graded') THEN 1 END) > 0 
                THEN 'Đã hoàn thành'
                ELSE 'Chưa làm'
            END AS completion_status,
            DATE_FORMAT(MAX(ea.submitted_at), '%d/%m/%Y') AS exam_date
        FROM exams e
        LEFT JOIN exam_attempts ea 
            ON e.id = ea.exam_id 
            AND ea.user_id = ?
        WHERE e.class_id = ?
        AND e.status IN ('published', 'upcoming', 'closed')
        GROUP BY e.id, e.title, e.name, e.created_at
        ORDER BY MAX(ea.submitted_at) DESC, e.created_at DESC;
    ");
    $stmt->bind_param("ii", $studentId, $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function getAllTopic($studentId, $classId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            ch.id AS chapter_id,
            ch.name AS chapter_name,             
            ch.title AS chapter_description,
            COUNT(l.id) AS total_chapter_lessons,
            COUNT(lp.lesson_id) AS completed_chapter_lessons,
            COALESCE(
                ROUND(
                    COUNT(lp.lesson_id) * 100.0 / NULLIF(COUNT(l.id), 0), 
                    0
                ), 
                0
            ) AS chapter_progress_percent
        FROM chapter ch
        LEFT JOIN lessons l ON ch.id = l.chapter_id
        LEFT JOIN lesson_progress lp 
            ON l.id = lp.lesson_id 
            AND lp.user_id = ?
            AND lp.is_completed = 1
        WHERE ch.class_id = ?
        GROUP BY ch.id, ch.name, ch.title, ch.order_index
        ORDER BY ch.order_index ASC, ch.id ASC;
    ");
    $stmt->bind_param("ii", $studentId, $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function startExamAttempt($examId, $studentId)
{
    global $db;
    $stmt = $db->prepare("
        INSERT INTO exam_attempts (exam_id, user_id, started_at, status)
        VALUES (?, ?, NOW(), 'in_progress')
    ");
    $stmt->bind_param("ii", $examId, $studentId);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}
