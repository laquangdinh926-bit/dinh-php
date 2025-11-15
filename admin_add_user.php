<?php
require 'config.php';

// Bảo vệ: Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // Lấy role từ form

    if ($password !== $confirm_password) {
        $message = "Mật khẩu không khớp!";
    } elseif (!in_array($role, ['user', 'admin'])) {
        $message = "Vai trò không hợp lệ!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)");
        try {
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':email' => $email ?: null,
                ':role' => $role
            ]);
            header("Location: admin_users.php?msg=added");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // duplicate entry
                $message = "Username hoặc email đã tồn tại!";
            } else {
                $message = "Lỗi: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tiêu đề của bạn</title> <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <h2>Thêm tài khoản mới</h2>
    <?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Xác nhận mật khẩu</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Vai trò (Role)</label>
            <select name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button class="btn btn-primary">Thêm mới</button>
        <a href="admin_users.php" class="btn btn-link">Hủy</a>
    </form>
</div>
</body>
</html>