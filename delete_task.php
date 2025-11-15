<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$task_id = $_GET['id'] ?? 0;

// --- LOGIC XÓA (ĐÃ SỬA) ---
// Admin có thể xóa bất kỳ task nào
// User chỉ có thể xóa task của mình

if ($_SESSION['role'] === 'admin') {
    // Admin không cần check user_id
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=:id");
    $params = [':id' => $task_id];
} else {
    // User phải check user_id
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=:id AND user_id=:user_id");
    $params = [':id' => $task_id, ':user_id' => $_SESSION['user_id']];
}

$stmt->execute($params);

// Kiểm tra xem có hàng nào bị ảnh hưởng không
if ($stmt->rowCount() > 0) {
    header("Location: dashboard.php?msg=deleted");
} else {
    // Nếu không có hàng nào bị xóa (do task không tồn tại, hoặc user cố xóa task của người khác)
    header("Location: dashboard.php?msg=notfound");
}
exit;

// Xóa code cũ bên dưới
// $stmt = $conn->prepare("SELECT * FROM tasks WHERE id=:id AND user_id=:user_id");
// ...
?>