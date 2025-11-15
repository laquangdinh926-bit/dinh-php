<?php
session_start(); // Bắt đầu session để lưu thông tin đăng nhập

$servername = "localhost";
$username = "root";
$password = "";
$database = "web";
$port = 3307; // Thêm cổng SQL của bạn vào đây

try {
    // Thêm $port vào chuỗi kết nối DSN
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // (Bạn có thể thêm dòng này để kiểm tra nhanh)
    // echo "Kết nối thành công!"; 

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>