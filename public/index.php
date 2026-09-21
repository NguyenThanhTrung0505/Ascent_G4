<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', '/final/public');
require_once ROOT_PATH . '/app/helpers/auth_helper.php';
$db = require_once ROOT_PATH . '/config/database.php';
$routes = require ROOT_PATH . '/config/routes.php';

$page   = $_GET['page']   ?? 'home';
$action = $_GET['action'] ?? 'index';
$key    = "{$page}.{$action}";
if (!isset($routes[$key])) {
    http_response_code(404);
    exit('
    <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <title>404 - Không tìm thấy trang</title>
        </head>
        <body>
            <h1>404 Không tìm thấy trang</h1>
            <a href="index.php?page=home&action=index">⬅ Quay về trang chủ</a>
        </body>
        </html>
    ');
}
require ROOT_PATH . $routes[$key];
