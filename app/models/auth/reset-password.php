<?php
function findUserByEmail(string $email)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user : null;
}

function updateUserPassword(string $email, string $password_hash)
{
    global $db;
    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $password_hash, $email);
    return $stmt->execute();
}
