<?php
function getExamList($teacherId, $limit = 10, $offset = 0)
{
    global $db;
    $sql = "
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
        LIMIT ? OFFSET ?;
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $teacherId, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTotalExams($teacherId)
{
    global $db;
    $sql = "SELECT COUNT(*) AS total_records FROM exams WHERE created_by = ?;";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    return (int)$stmt->get_result()->fetch_row()[0];
}

function deleteExamTransaction($examId, $teacherId)
{
    global $db;
    try {
        $db->begin_transaction();
        $checkStmt = $db->prepare("SELECT id FROM exams WHERE id = ? AND created_by = ?");
        $checkStmt->bind_param("ii", $examId, $teacherId);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows === 0) {
            throw new Exception("Không có quyền xóa hoặc đề thi không tồn tại.");
        }

        $db->query("DELETE ao FROM answer_options ao INNER JOIN questions q ON ao.question_id = q.id WHERE q.exam_id = $examId");

        $db->query("DELETE FROM questions WHERE exam_id = $examId");

        $db->query("DELETE FROM exams WHERE id = $examId");

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}
