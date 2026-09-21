<?php
function getListUser($role, $status, $search, $limit, $offset)
{
    global $db;
    $searchWildcard = $search ? "%{$search}%" : null;
    $stmt = $db->prepare(
        "
        SELECT 
            u.id,
            u.username,
            u.email,
            u.role,
            u.status,
            u.created_at
        FROM users u
        WHERE 
            u.role = ?
            AND (? IS NULL OR u.status = ?)
            AND (
                ? IS NULL 
                OR u.username LIKE ?
                OR u.email LIKE ?
            )
        ORDER BY u.id DESC
        LIMIT ? OFFSET ?;
        "
    );
    $stmt->bind_param(
        "ssssssii",
        $role,
        $status,
        $status,
        $search,
        $searchWildcard,
        $searchWildcard,
        $limit,
        $offset
    );
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $result;
}

function getLimit($role, $status, $search)
{
    global $db;
    $stmt = $db->prepare("
        SELECT COUNT(*) AS total_records
        FROM users u
        WHERE 
            u.role = ?
            AND (? IS NULL OR u.status = ?)
            AND (
                ? IS NULL 
                OR u.username LIKE CONCAT('%', ?, '%')
                OR u.email LIKE CONCAT('%', ?, '%')
            );
    ");
    $stmt->bind_param("ssssss", $role, $status, $status, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return (int)$result['total_records'];
}

function addUser($username, $passwordHash, $email, $role, $status = 'active')
{
    global $db;
    $stmt = $db->prepare("INSERT INTO users (username, password_hash, email, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $passwordHash, $email, $role, $status);
    return $stmt->execute();
}

function updateUser($userId, $username, $email)
{
    global $db;
    $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $userId);
    return $stmt->execute();
}
function updateStatus($userId, $status)
{
    global $db;
    $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $userId);
    return $stmt->execute();
}
function deleteUser($userId, $role, $adminId)
{
    global $db;
    $db->begin_transaction();
    try {
        if ($role === 'teacher') {
            $db->query("UPDATE classes SET teacher_id = $adminId WHERE teacher_id = $userId");
            $db->query("UPDATE chapter SET created_by = $adminId WHERE created_by = $userId");
            $db->query("UPDATE lessons SET created_by = $adminId WHERE created_by = $userId");
            $db->query("UPDATE exams SET created_by = $adminId WHERE created_by = $userId");
        }
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}
