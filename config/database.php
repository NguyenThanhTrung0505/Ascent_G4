<?php
try {
    $link = mysqli_connect("localhost", "root", "", "db_ltw");
    mysqli_set_charset($link, "utf8mb4");
    return $link;
} catch (mysqli_sql_exception $e) {
    die("Lỗi kết nối MySQL: " . $e->getMessage());
}
