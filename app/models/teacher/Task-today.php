<?php
function getExamList($teacherId, $limit = 5, $offset = 0)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT 
                e.id AS exam_id,
                e.name AS exam_title,
                e.duration,
                e.created_at,
                COUNT(q.id) AS total_questions
            FROM exams e
            LEFT JOIN questions q ON e.id = q.exam_id
            WHERE e.created_by = ?
            GROUP BY 
                e.id, e.name, e.duration, e.created_at
            ORDER BY 
                e.created_at DESC
            LIMIT ?
            OFFSET ?;
        "
    );
    $stmt->bind_param("iii", $teacherId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function getAllExam($teacherId)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT COUNT(*) AS total_records
            FROM exams 
            WHERE created_by = ?;
        "
    );
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0];
}

function getTopicList($teacherId, $limit = 5, $offset = 0)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT 
                c.id AS chapter_id,
                c.name AS chapter_name,
                c.created_at,
                COUNT(l.id) AS total_lessons
            FROM chapter c
            LEFT JOIN lessons l ON c.id = l.chapter_id
            WHERE c.created_by = ? 
            GROUP BY 
                c.id, c.name, c.created_at, c.order_index
            ORDER BY 
                c.order_index ASC, 
                c.created_at DESC
            LIMIT ?
            OFFSET ?;
        "
    );
    $stmt->bind_param("iii", $teacherId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllTopic($teacherId)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT COUNT(*) AS total_records
            FROM chapter 
            WHERE created_by = ?;
        "
    );
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0];
}

function getTodayList($teacherId)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT 
            id AS task_id,
            name AS task_name,
            end_time AS due_date
        FROM exams
        WHERE created_by = ?
        AND end_time IS NOT NULL               
        AND end_time >= CURRENT_DATE()
        ORDER BY 
            end_time ASC
        LIMIT 5
        OFFSET 0;
        "
    );
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAllToday($teacherId)
{
    global $db;
    $stmt = $db->prepare(
        "
            SELECT COUNT(id) AS total_tasks
            FROM exams
            WHERE created_by = ? 
                AND end_time IS NOT NULL 
                AND end_time >= CURRENT_DATE();
        "
    );
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return $result[0];
}
