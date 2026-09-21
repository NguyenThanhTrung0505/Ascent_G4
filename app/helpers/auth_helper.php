<?php
function requireRole(string $role)
{
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== $role) {
        header('Location: index.php?page=auth&action=login');
        exit;
    }
}

function requireLogin()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=auth&action=login');
        exit;
    }
}
