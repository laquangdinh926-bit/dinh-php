<?php
require 'config.php';

// Bảo vệ: Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$search = $_GET['search'] ?? '';
$msg = $_GET['msg'] ?? '';

// Logic tìm kiếm
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE :search OR email LIKE :search ORDER BY id DESC");
    $stmt->execute([':search' => "%$search%"]);
} else {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
    $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý tài khoản</h2>
        <a href="dashboard.php" class="btn btn-secondary">Quay về Dashboard</a>
    </div>

    <?php if($msg === 'added'): ?>
        <div class="alert alert-success">Thêm tài khoản thành công!</div>
    <?php elseif($msg === 'updated'): ?>
        <div class="alert alert-success">Cập nhật tài khoản thành công!</div>
    <?php elseif($msg === 'deleted'): ?>
        <div class="alert alert-success">Xóa tài khoản thành công!</div>
    <?php elseif($msg === 'self_delete_error'): ?>
        <div class="alert alert-danger">Bạn không thể tự xóa chính mình!</div>
    <?php elseif($msg === 'notfound'): ?>
        <div class="alert alert-danger">Tài khoản không tồn tại!</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="admin_add_user.php" class="btn btn-success">Thêm tài khoản mới</a>
        <form method="get" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Tìm username hoặc email..." value="<?=htmlspecialchars($search)?>">
            <button class="btn btn-primary">Tìm</button>
        </form>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Vai trò (Role)</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
                <tr>
                    <td><?=$user['id']?></td>
                    <td><?=htmlspecialchars($user['username'])?></td>
                    <td><?=htmlspecialchars($user['email'])?></td>
                    <td><?=htmlspecialchars($user['role'])?></td>
                    <td>
                        <a href="admin_edit_user.php?id=<?=$user['id']?>" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="admin_delete_user.php?id=<?=$user['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá user này? Mọi công việc (tasks) của họ cũng sẽ bị xóa theo.')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(count($users) == 0): ?>
                <tr><td colspan="5" class="text-center">Không tìm thấy tài khoản nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>