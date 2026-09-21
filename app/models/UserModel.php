<?php

function findUserByEmail(string $email)
{
    global $db;
    $stmt = $db->prepare(
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? $user : null;
}
