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

function createUser(string $username, string $email, string $password_hash, string $role)
{
    global $db;
    $stmt = $db->prepare(
        "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $username, $email, $password_hash, $role);
    return $stmt->execute();
}
