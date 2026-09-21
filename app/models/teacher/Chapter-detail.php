<?php

function getLessonList($chapterId, $limit = 10, $offset = 0)
{
    global $db;
    $stmt = $db->prepare(
        "
        SELECT 
            id AS lesson_id,
            name AS lesson_name,
            title AS lesson_description,
            order_index,
            content,
            file_name
        FROM lessons
        WHERE chapter_id = ?
        ORDER BY 
            order_index ASC,
            created_at ASC
        LIMIT ? OFFSET ?;
        "
    );
    $stmt->bind_param("iii", $chapterId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTotalLessons($chapter_id)
{
    global $db;
    $stmt = $db->prepare("SELECT COUNT(id) AS total_lessons FROM lessons WHERE chapter_id = ?;");
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    return (int)$result[0];
}

function getChapterInfo($chapter_id)
{
    global $db;
    $stmt = $db->prepare("SELECT name AS ten_chuong, title as mo_ta FROM chapter WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $chapter_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}

function addLesson($data)
{
    global $db;
    $stmt = $db->prepare(
        "INSERT INTO lessons (chapter_id, created_by, name, title, order_index, content, file_name, file_type, file_data)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);"
    );
    $stmt->bind_param(
        "iississss",
        $data['chapter_id'],
        $data['created_by'],
        $data['name'],
        $data['title'],
        $data['order_index'],
        $data['content'],
        $data['file_name'],
        $data['file_type'],
        $data['file_data']
    );

    if ($stmt->execute()) {
        return $stmt->insert_id;
    }
    return false;
}

function updateLesson($lessonId, $teacherId, $data)
{
    global $db;
    if (isset($data['file_name']) && $data['file_name'] !== null) {
        $stmt = $db->prepare(
            "UPDATE lessons SET name=?, title=?, order_index=?, content=?, file_name=?, file_type=?, file_data=? 
             WHERE id=? AND created_by=?;"
        );
        $stmt->bind_param(
            "ssissssii",
            $data['name'],
            $data['title'],
            $data['order_index'],
            $data['content'],
            $data['file_name'],
            $data['file_type'],
            $data['file_data'],
            $lessonId,
            $teacherId
        );
    } else {
        $stmt = $db->prepare(
            "UPDATE lessons SET name=?, title=?, order_index=?, content=? 
             WHERE id=? AND created_by=?;"
        );
        $stmt->bind_param(
            "ssisii",
            $data['name'],
            $data['title'],
            $data['order_index'],
            $data['content'],
            $lessonId,
            $teacherId
        );
    }
    return $stmt->execute();
}

function deleteLesson($lessonId, $teacherId)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM lessons WHERE id = ? AND created_by = ?;");
    $stmt->bind_param("ii", $lessonId, $teacherId);
    return $stmt->execute();
}
