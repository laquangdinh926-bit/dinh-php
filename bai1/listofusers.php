<?php
session_start();
require_once "db.php"; 

// Nếu chưa đăng nhập thì quay về login
if (!isset($_SESSION['logged'])) {
    header("Location: login.php");
    exit;
}

$db = new DbHelper();
$users = $db->select("SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-primary">📋 Danh sách Tài khoản</h2>
        <a href="logout.php" class="btn btn-danger">Đăng xuất</a>
    </div>

    <div class="card shadow mt-3">
        <div class="card-body">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!$users): ?>
                    <tr><td colspan="4" class="text-muted">Không có tài khoản nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u->id; ?></td>
                            <td><?php echo htmlspecialchars($u->username); ?></td>
                            <td><?php echo htmlspecialchars($u->password); ?></td>
                            <td>
                                <a href="editUser.php?id=<?php echo $u->id; ?>" class="btn btn-warning btn-sm">✏ Sửa</a>
                                <a href="deleteUser.php?id=<?php echo $u->id; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">🗑 Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer text-end">
            <a href="newuserinput.php" class="btn btn-success">➕ Thêm tài khoản</a>
        </div>
    </div>
</div>

</body>
</html>
