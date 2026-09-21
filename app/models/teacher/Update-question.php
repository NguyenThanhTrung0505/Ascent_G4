<?php

function getExamFullDetails($examId)
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->bind_param("i", $examId);
    $stmt->execute();
    $exam = $stmt->get_result()->fetch_assoc();

    if (!$exam) return null;
    $stmt = $db->prepare("SELECT * FROM questions WHERE exam_id = ?");
    $stmt->bind_param("i", $examId);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach ($questions as &$q) {
        $stmt = $db->prepare("SELECT * FROM answer_options WHERE question_id = ?");
        $stmt->bind_param("i", $q['id']);
        $stmt->execute();
        $q['options'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    $exam['questions'] = $questions;
    return $exam;
}

function updateExamTransaction($examId, $examData, $questionsList)
{
    global $db;
    try {
        $db->begin_transaction();

        $sql = "UPDATE exams SET name=?, class_id=?, duration=?, attempts=?, start_time=?, end_time=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param(
            "siiissi",
            $examData['name'],
            $examData['class_id'],
            $examData['duration'],
            $examData['attempts'],
            $examData['start_time'],
            $examData['end_time'],
            $examId
        );
        $stmt->execute();
        $db->query("DELETE ao FROM answer_options ao INNER JOIN questions q ON ao.question_id = q.id WHERE q.exam_id = $examId");
        $db->query("DELETE FROM questions WHERE exam_id = $examId");
        foreach ($questionsList as $question) {
            $questionId = update_insertQuestion($examId, $question['content'], $question['explanation']);
            update_insertAnswers($questionId, $question['options']);
        }

        $db->commit();
        return ["status" => "success", "message" => "Cập nhật đề thi thành công!"];
    } catch (Exception $e) {
        $db->rollback();
        return ["status" => "error", "message" => "Lỗi cập nhật: " . $e->getMessage()];
    }
}

function update_insertQuestion($examId, $content, $explanation)
{
    global $db;
    $sql = "INSERT INTO questions (exam_id, content, explanation) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iss", $examId, $content, $explanation);
    $stmt->execute();
    return $db->insert_id;
}

function update_insertAnswers($questionId, $options)
{
    global $db;
    $sql = "INSERT INTO answer_options (question_id, content, is_correct) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    foreach ($options as $option) {
        $is_correct = $option['is_correct'] ? 1 : 0;
        $stmt->bind_param("isi", $questionId, $option['content'], $is_correct);
        $stmt->execute();
    }
}
