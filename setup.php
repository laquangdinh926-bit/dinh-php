<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "web";
$port = 3307; // Giữ lại port 3307 của bạn

try {
    // Kết nối ở đây cũng cần port
    $conn = new PDO("mysql:host=$servername;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created successfully<br>";

    $conn->exec("USE $database");

    // Sửa bảng users: Thêm cột 'role'
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE,
        /* THÊM DÒNG NÀY */
        role ENUM('user','admin') DEFAULT 'user', 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->exec($sqlUsers);
    echo "Table 'users' created successfully (với cột role)<br>";

    // Bảng tasks giữ nguyên
    $sqlTasks = "CREATE TABLE IF NOT EXISTS tasks (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        status ENUM('pending','in_progress','completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->exec($sqlTasks);
    echo "Table 'tasks' created successfully<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>