<?php
function createExamTransaction($examData, $questionsList)
{
    global $db;
    try {
        $db->begin_transaction();
        $examId = insertExam($examData);
        foreach ($questionsList as $question) {
            $questionId = insertQuestion($examId, $question['content'], $question['explanation']);
            insertAnswers($questionId, $question['options']);
        }
        $db->commit();
        return ["status" => "success", "message" => "Tạo đề thi thành công!"];
    } catch (Exception $e) {
        $db->rollback();
        return ["status" => "error", "message" => "Lỗi: " . $e->getMessage()];
    }
}

function insertExam($data)
{
    global $db;
    $sql = "INSERT INTO exams (class_id, created_by, name, duration, attempts, start_time, end_time, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'upcoming')";
    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "iisiiss",
        $data['class_id'],
        $data['created_by'],
        $data['name'],
        $data['duration'],
        $data['attempts'],
        $data['start_time'],
        $data['end_time']
    );
    $stmt->execute();
    return $db->insert_id;
}

function insertQuestion($examId, $content, $explanation)
{
    global $db;
    $sql = "INSERT INTO questions (exam_id, content, explanation) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iss", $examId, $content, $explanation);
    $stmt->execute();
    return $db->insert_id;
}

function insertAnswers($questionId, $options)
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
