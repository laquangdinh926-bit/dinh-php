<?php
require 'config.php';

// Bảo vệ: Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$user_id_to_delete = $_GET['id'] ?? 0;

// Cấm admin tự xóa chính mình
if ($user_id_to_delete == $_SESSION['user_id']) {
    header("Location: admin_users.php?msg=self_delete_error");
    exit;
}

// Kiểm tra xem user có tồn tại không
$stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
$stmt->execute([':id'=>$user_id_to_delete]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    header("Location: admin_users.php?msg=notfound");
    exit;
}

// Tiến hành xóa (Nhờ CSDL đã set ON DELETE CASCADE, mọi tasks của user này cũng sẽ bị xóa)
$stmt = $conn->prepare("DELETE FROM users WHERE id=:id");
$stmt->execute([':id'=>$user_id_to_delete]);

header("Location: admin_users.php?msg=deleted");
exit;
?>