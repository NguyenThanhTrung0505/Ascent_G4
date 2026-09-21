<?php
function getLessonNumber($topicId, $studentId)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT 
            ch.id AS chapter_id,
            ch.name AS chapter_name,
            c.id AS class_id,
            c.name AS class_name, 
            COUNT(l.id) AS total_lessons,
            COUNT(CASE WHEN lp.is_completed = 1 THEN 1 END) AS completed_lessons,
            CONCAT(
                COUNT(CASE WHEN lp.is_completed = 1 THEN 1 END), 
                '/', 
                COUNT(l.id), 
                ' bài'
            ) AS completion_text
        FROM chapter ch
        JOIN classes c ON ch.class_id = c.id
        LEFT JOIN lessons l ON ch.id = l.chapter_id
        LEFT JOIN lesson_progress lp 
            ON l.id = lp.lesson_id 
            AND lp.user_id = ?                      
        WHERE ch.id = ?                    
        GROUP BY ch.id, ch.name, c.id, c.name;
        "
    );
    $stmt->bind_param("ii", $studentId, $topicId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
function getListLesson($topicId, $studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            l.id AS lesson_id,
            l.name AS lesson_name,               
            l.title AS lesson_description,       
            l.order_index,
            COALESCE(lp.is_completed, 0) AS is_completed,
            CASE 
                WHEN lp.is_completed = 1 THEN 'Đã học'
                ELSE 'Học ngay'
            END AS status_label,
            lp.completed_at                    
        FROM lessons l
        LEFT JOIN lesson_progress lp 
            ON l.id = lp.lesson_id 
            AND lp.user_id = ?
        WHERE l.chapter_id = ?
        ORDER BY l.order_index ASC, l.id ASC;
    ");
    $stmt->bind_param("ii", $studentId, $topicId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function updateLessonProgress($studentId, $lessonId, $isCompleted)
{
    global $db;
    $completedStatus = $isCompleted ? 1 : 0;
    $checkStmt = $db->prepare("SELECT lesson_id FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
    $checkStmt->bind_param("ii", $studentId, $lessonId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows > 0) {
        $updateStmt = $db->prepare("
            UPDATE lesson_progress 
            SET is_completed = ?, 
                completed_at = CASE WHEN ? = 1 THEN NOW() ELSE NULL END
            WHERE user_id = ? AND lesson_id = ?
        ");
        $updateStmt->bind_param("iiii", $completedStatus, $completedStatus, $studentId, $lessonId);
        return $updateStmt->execute();
    } else {
        $insertStmt = $db->prepare("
            INSERT INTO lesson_progress (user_id, lesson_id, is_completed, completed_at) 
            VALUES (?, ?, ?, CASE WHEN ? = 1 THEN NOW() ELSE NULL END)
        ");
        $insertStmt->bind_param("iiii", $studentId, $lessonId, $completedStatus, $completedStatus);
        return $insertStmt->execute();
    }
}
