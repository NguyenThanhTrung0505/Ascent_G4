<?php
function getChapterList($classId, $limit = 10, $offset = 0)
{
    global $db;
    $sql = "
        SELECT 
            ch.id AS chapter_id,
            ch.name AS chapter_name,
            ch.title AS chapter_title,
            ch.order_index,
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
        GROUP BY ch.id, ch.name, ch.title, ch.order_index, class_info.total_students
        ORDER BY ch.order_index ASC, ch.created_at ASC
        LIMIT ? OFFSET ?;
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iiiii", $classId, $classId, $classId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTotalChapters($classId)
{
    global $db;
    $stmt = $db->prepare("SELECT COUNT(id) FROM chapter WHERE class_id = ?;");
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    return (int)$stmt->get_result()->fetch_row()[0];
}

function addChapter($data)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO chapter (class_id, created_by, name, title, order_index) VALUES (?, ?, ?, ?, ?);");
    $stmt->bind_param("iissi", $data['class_id'], $data['created_by'], $data['name'], $data['title'], $data['order_index']);
    return $stmt->execute() ? $stmt->insert_id : false;
}

function updateChapter($chapterId, $teacherId, $data)
{
    global $db;
    $stmt = $db->prepare("UPDATE chapter SET name = ?, title = ?, order_index = ? WHERE id = ? AND created_by = ?;");
    $stmt->bind_param("ssiii", $data['name'], $data['title'], $data['order_index'], $chapterId, $teacherId);
    return $stmt->execute();
}

function deleteChapter($chapterId, $teacherId)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM chapter WHERE id = ? AND created_by = ?;");
    $stmt->bind_param("ii", $chapterId, $teacherId);
    return $stmt->execute();
}
