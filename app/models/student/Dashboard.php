<?php
function getJoinClassNumber($studentId)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT COUNT(cs.class_id) AS total_enrolled_classes
        FROM class_students cs
        WHERE cs.user_id = ?
        "
    );
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_enrolled_classes'] ?? 0;
}
function getJoinSubject($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS subject_list
        FROM class_students cs
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        WHERE cs.user_id = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return ($result['subject_list'] ?? 'Chưa tham gia môn nào');
}

function getExamCurrent($studentId)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT COUNT(e.id) AS current
        FROM exams e
        JOIN class_students cs ON e.class_id = cs.class_id
        WHERE cs.user_id = ?
        AND e.status IN ('published', 'upcoming')
        AND YEARWEEK(e.end_time, 1) = YEARWEEK(NOW(), 1)
        AND NOT EXISTS (
        SELECT 1 
        FROM exam_attempts ea 
        WHERE ea.exam_id = e.id 
        AND ea.user_id = ?
        AND ea.status IN ('submitted', 'graded')
        )
    "
    );
    $stmt->bind_param("ii", $studentId, $studentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['current_exams'] ?? 0;
}

function getExamUpcoming($studentId)
{
    global $db;
    $stmt = $db->prepare("
    
        SELECT COUNT(e.id) AS upcoming_exams_count
        FROM exams e
        JOIN class_students cs ON e.class_id = cs.class_id
        WHERE cs.user_id = ?
        AND (e.status = 'upcoming' OR e.start_time > NOW())
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['upcoming_exams_count'] ?? 0;
}

function getGradeAvg($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT ROUND(AVG(best_scores.max_score), 1) AS avg_score
        FROM (
        SELECT MAX(ea.score) AS max_score
        FROM exam_attempts ea
        WHERE ea.user_id = ?
        AND ea.status = 'graded'
        AND ea.score IS NOT NULL
        GROUP BY ea.exam_id
        ) AS best_scores
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['avg_score'] ?? 0.0;
}
function getUpcomingTasksList($studentId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            e.id,
            c.name AS subject,
            DATE_FORMAT(e.end_time, '%d/%m/%Y %H:%i') AS deadline,
            e.name AS badge,
            'blue' AS theme,
            0 AS progress
        FROM exams e
        JOIN classes c ON e.class_id = c.id
        JOIN subjects s ON c.subject_id = s.id
        JOIN class_students cs ON e.class_id = cs.class_id
        WHERE cs.user_id = ?
          AND e.status IN ('published', 'upcoming')
          AND e.end_time >= NOW()
        ORDER BY e.end_time ASC
        LIMIT 5
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
