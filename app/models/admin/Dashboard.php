<?php
function getBage()
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT 
            COUNT(DISTINCT CASE WHEN u.role = 'teacher' THEN u.id END) AS total_teachers,
            COUNT(DISTINCT CASE WHEN u.role = 'student' THEN u.id END) AS total_students,
            (SELECT COUNT(*) FROM classes) AS total_classes,
            (SELECT COUNT(*) FROM exams) AS total_exams
        FROM users u;
        "
    );
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}
function getActive()
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            u.id AS user_id,
            u.username,
            activity.action_type,
            activity.description,
            activity.activity_time
        FROM (
            SELECT 
                ea.user_id,
                'EXAM' AS action_type,
                CONCAT('Đã thực hiện bài thi: ', e.name) AS description,
                COALESCE(ea.submitted_at, ea.started_at) AS activity_time
            FROM exam_attempts ea
            JOIN exams e ON ea.exam_id = e.id
            UNION ALL
            SELECT 
                lp.user_id,
                'LESSON' AS action_type,
                CONCAT('Đã hoàn thành bài học: ', l.name) AS description,
                lp.completed_at AS activity_time
            FROM lesson_progress lp
            JOIN lessons l ON lp.lesson_id = l.id
            WHERE lp.is_completed = TRUE AND lp.completed_at IS NOT NULL
            UNION ALL
            SELECT 
                cs.user_id,
                'CLASS_JOIN' AS action_type,
                CONCAT('Đã tham gia lớp: ', c.name) AS description,
                cs.joined_at AS activity_time
            FROM class_students cs
            JOIN classes c ON cs.class_id = c.id
        ) AS activity
        JOIN users u ON activity.user_id = u.id
        ORDER BY activity.activity_time DESC
        LIMIT 10;
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function getActiveWeek()
{
    global $db;
    $stmt = $db->prepare("
        WITH current_week_activities AS (
            SELECT started_at AS act_time 
            FROM exam_attempts
            WHERE started_at >= DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY)
            AND started_at < DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY), INTERVAL 7 DAY)

            UNION ALL

            SELECT completed_at AS act_time 
            FROM lesson_progress
            WHERE completed_at >= DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY)
            AND completed_at < DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY), INTERVAL 7 DAY)
            AND is_completed = TRUE

            UNION ALL

            SELECT joined_at AS act_time 
            FROM class_students
            WHERE joined_at >= DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY)
            AND joined_at < DATE_ADD(DATE_SUB(CURRENT_DATE, INTERVAL WEEKDAY(CURRENT_DATE) DAY), INTERVAL 7 DAY)
        )
        SELECT 
            WEEKDAY(act_time) AS day_index,
            CASE WEEKDAY(act_time)
                WHEN 0 THEN 'T2'
                WHEN 1 THEN 'T3'
                WHEN 2 THEN 'T4'
                WHEN 3 THEN 'T5'
                WHEN 4 THEN 'T6'
                WHEN 5 THEN 'T7'
                WHEN 6 THEN 'CN'
            END AS day_label,
            COUNT(*) AS total_activities
        FROM current_week_activities
        GROUP BY WEEKDAY(act_time), day_label
        ORDER BY day_index ASC;
    ");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
