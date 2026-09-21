<?php
function getTime($studentId, $attemptId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            ea.id AS attempt_id,
            e.id AS exam_id,
            COALESCE(e.title, e.name) AS exam_title,
            e.duration AS total_duration_minutes,
            ea.started_at,
            GREATEST(
                0, 
                TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ea.started_at, INTERVAL e.duration MINUTE))
            ) AS remaining_seconds,
            ea.status AS attempt_status
        FROM exam_attempts ea
        JOIN exams e ON ea.exam_id = e.id
        WHERE ea.id = ?
        AND ea.user_id = ?
        AND ea.status = 'in_progress'
        LIMIT 1;
    ");
    $stmt->bind_param("ii", $attemptId, $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllQuestionsWithOptions($examId, $attemptId)
{
    global $db;
    $stmt = $db->prepare("
        SELECT 
            q.id AS question_id,
            q.content AS question_content,
            ao.id AS option_id,
            ao.content AS option_content,
            CASE 
                WHEN sa.selected_option_id = ao.id THEN 1 
                ELSE 0 
            END AS is_selected
        FROM questions q
        JOIN answer_options ao ON q.id = ao.question_id
        LEFT JOIN student_answers sa 
            ON q.id = sa.question_id 
            AND sa.attempt_id = ?
        WHERE q.exam_id = ?
        ORDER BY q.id ASC, ao.id ASC;
    ");
    $stmt->bind_param("ii", $attemptId, $examId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function saveStudentAnswer($attemptId, $questionId, $optionId)
{
    global $db;
    $stmt = $db->prepare("
        INSERT INTO student_answers (attempt_id, question_id, selected_option_id)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            selected_option_id = VALUES(selected_option_id)
    ");
    $stmt->bind_param("iii", $attemptId, $questionId, $optionId);
    return $stmt->execute();
}

function finishExamAttempt($attemptId, $studentId)
{
    global $db;
    $stmt = $db->prepare("
        UPDATE exam_attempts 
        SET status = 'submitted'
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $attemptId, $studentId);
    return $stmt->execute();
}

function gradeExamAttempt($attemptId, $studentId, $examId)
{
    global $db;
    $db->begin_transaction();
    try {
        $stmtMarkCorrect = $db->prepare("
            UPDATE student_answers sa
            JOIN answer_options ao ON sa.selected_option_id = ao.id
            SET sa.is_correct = ao.is_correct
            WHERE sa.attempt_id = ?
        ");
        $stmtMarkCorrect->bind_param("i", $attemptId);
        $stmtMarkCorrect->execute();
        $stmtStats = $db->prepare("
            SELECT 
                (SELECT COUNT(id) FROM questions WHERE exam_id = ?) AS total_questions,
                (SELECT COUNT(id) FROM student_answers WHERE attempt_id = ? AND is_correct = 1) AS correct_answers
        ");
        $stmtStats->bind_param("ii", $examId, $attemptId);
        $stmtStats->execute();
        $stats = $stmtStats->get_result()->fetch_assoc();
        $totalQuestions = (int)$stats['total_questions'];
        $correctAnswers = (int)$stats['correct_answers'];
        $score = ($totalQuestions > 0) ? round(($correctAnswers / $totalQuestions) * 10, 2) : 0.00;
        $stmtUpdate = $db->prepare("
            UPDATE exam_attempts 
            SET score = ?, 
                status = 'graded', 
                submitted_at = NOW()
            WHERE id = ? AND user_id = ? AND status = 'in_progress'
        ");
        $stmtUpdate->bind_param("dii", $score, $attemptId, $studentId);
        $stmtUpdate->execute();
        $db->commit();
        return [
            'score'          => $score,
            'correctAnswers' => $correctAnswers,
            'totalQuestions' => $totalQuestions
        ];
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}
