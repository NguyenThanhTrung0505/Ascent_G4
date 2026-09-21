<?php

function getTeacherDashboardStats($teacherId)
{
    global $db;
    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM classes WHERE teacher_id = ?) AS so_lop,
            (SELECT COUNT(*) FROM exams WHERE created_by = ?) AS so_de,
            (SELECT COUNT(*) FROM chapter WHERE created_by = ?) AS so_chuong,
            (SELECT COUNT(*) FROM exams WHERE created_by = ? AND DATE(end_time) = CURDATE()) AS so_nhiem_vu
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iiii", $teacherId, $teacherId, $teacherId, $teacherId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return [
        'so_chuong'   => (int) ($row['so_chuong'] ?? 0),
        'so_de'       => (int) ($row['so_de'] ?? 0),
        'so_lop'      => (int) ($row['so_lop'] ?? 0),
        'so_nhiem_vu' => (int) ($row['so_nhiem_vu'] ?? 0)
    ];
}


function getOngoingExams($teacherId, $limit = 3)
{
    global $db;
    $sql = "
        SELECT 
            e.id,
            e.title AS tieu_de,
            e.end_time AS due_date,
            c.name AS ten_lop,
            (
                SELECT COUNT(*)
                FROM class_students cs
                WHERE cs.class_id = e.class_id
            ) AS tong_so_hoc_sinh,
            (
                SELECT COUNT(DISTINCT ea.user_id)
                FROM exam_attempts ea
                WHERE ea.exam_id = e.id
                AND ea.status IN ('submitted', 'graded')
            ) AS so_da_hoan_thanh
        FROM exams e
        JOIN classes c 
            ON e.class_id = c.id
        WHERE c.teacher_id = ?
        AND e.status = 'published'
        AND (e.end_time IS NULL OR e.end_time >= NOW())
        ORDER BY e.end_time ASC
        LIMIT ?
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param(
        "ii",
        $teacherId,
        $limit
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
