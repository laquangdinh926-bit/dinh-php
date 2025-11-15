<?php
require 'config.php';

// Bảo vệ: Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_GET['id'] ?? 0;
$message = '';

// Lấy thông tin user cần sửa
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_users.php?msg=notfound");
    exit;
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    $params = [
        ':username' => $username,
        ':email' => $email ?: null,
        ':role' => $role,
        ':id' => $user_id
    ];
    
    // Chỉ cập nhật mật khẩu NẾU admin có nhập mật khẩu mới
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = :username, email = :email, role = :role, password = :password WHERE id = :id";
        $params[':password'] = $hashedPassword;
    } else {
        $sql = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        header("Location: admin_users.php?msg=updated");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $message = "Username hoặc email đã tồn tại!";
        } else {
            $message = "Lỗi: " . $e->getMessage();
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
    <h2>Cập nhật tài khoản: <?=htmlspecialchars($user['username'])?></h2>
    <?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($user['username'])?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($user['email'])?>">
        </div>
        <div class="mb-3">
            <label>Vai trò (Role)</label>
            <select name="role" class="form-select">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <hr>
        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <input type="password" name="password" class="form-control">
            <small class="form-text text-muted">Để trống nếu không muốn thay đổi mật khẩu.</small>
        </div>
        
        <button class="btn btn-primary">Cập nhật</button>
        <a href="admin_users.php" class="btn btn-link">Hủy</a>
    </form>
</div>
</body>
</html>