<?php
function getLessonInfo($studentId, $lessonId)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT 
            l.id AS lesson_id,
            l.name AS lesson_name,
            l.title AS lesson_title,
            l.content AS lesson_content,
            l.file_name,
            c.id AS chapter_id,
            c.name AS chapter_name,
            cls.id AS class_id,
            cls.name AS class_name,
            s.name AS subject_name,
            COALESCE(lp.is_completed, FALSE) AS is_completed
        FROM lessons l
        JOIN chapter c ON l.chapter_id = c.id
        JOIN classes cls ON c.class_id = cls.id
        JOIN subjects s ON cls.subject_id = s.id
        LEFT JOIN lesson_progress lp 
            ON lp.lesson_id = l.id 
            AND lp.user_id = ?
        WHERE l.id = ?;
        "
    );
    $stmt->bind_param("ii", $studentId, $lessonId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}

function handleButtonDone($studentId, $lessonId)
{
    global $db;
    $stmt = $db->prepare("
        INSERT INTO lesson_progress (user_id, lesson_id, is_completed, completed_at)
        VALUES (?, ?, 1, CURRENT_TIMESTAMP)
        ON DUPLICATE KEY UPDATE 
        is_completed = 1,
        completed_at = CURRENT_TIMESTAMP;
    ");
    $stmt->bind_param("ii", $studentId, $lessonId);
    return $stmt->execute();
}
