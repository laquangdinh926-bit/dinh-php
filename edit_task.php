<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$task_id = $_GET['id'] ?? 0;
$all_users = []; // Khởi tạo mảng user

// --- LOGIC LẤY THÔNG TIN (ĐÃ SỬA) ---
// Admin có thể sửa bất kỳ task nào (của user, của mình, hoặc public)
// User chỉ có thể sửa task của chính mình
if ($_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute([':id'=>$task_id]);
    
    // BƯỚC 1: LẤY DANH SÁCH USERS CHO ADMIN
    $stmt_users = $conn->prepare("SELECT id, username FROM users ORDER BY username ASC");
    $stmt_users->execute();
    $all_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
    
} else {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id'=>$task_id, ':user_id'=>$_SESSION['user_id']]);
}
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: dashboard.php?msg=notfound");
    exit;
}
// --- KẾT THÚC SỬA ---


// Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'] ?: null;
    $status = $_POST['status'];

    // --- LOGIC CẬP NHẬT (ĐÃ SỬA) ---
    $params = [
        ':title' => $title,
        ':description' => $description,
        ':due_date' => $due_date,
        ':status' => $status,
        ':id' => $task_id
    ];
    
    // Câu lệnh SET cơ bản
    $sql_set_clause = "title = :title, description = :description, due_date = :due_date, status = :status";

    // Logic cho Admin
    if ($_SESSION['role'] === 'admin') {
        
        // BƯỚC 3: XỬ LÝ VIỆC THAY ĐỔI SỞ HỮU (OWNERSHIP)
        if (isset($_POST['owner_id'])) {
            $owner_id = $_POST['owner_id'];
            $sql_set_clause .= ", user_id = :owner_user_id"; // Thêm vào câu lệnh SET
            
            if ($owner_id === 'public') {
                $params[':owner_user_id'] = null; // Set là CÔNG VIỆC CHUNG
            } else {
                $params[':owner_user_id'] = (int)$owner_id; // Gán cho user cụ thể
            }
        }
        
        $sql = "UPDATE tasks SET $sql_set_clause WHERE id = :id";
    
    // Logic cho User (không được đổi sở hữu)
    } else {
        $sql = "UPDATE tasks SET $sql_set_clause WHERE id = :id AND user_id = :user_id";
        $params[':user_id'] = $_SESSION['user_id']; // Thêm user_id cho mệnh đề WHERE
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    // --- KẾT THÚC SỬA ---
    
    // Quay về trang danh sách công việc
    header("Location: dashboard.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật công việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm mx-auto" style="max-width: 600px; padding: 2rem;">
        <h4 class="mb-3">Cập nhật công việc</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tiêu đề:</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mô tả:</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Ngày hết hạn:</label>
                <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($task['due_date']) ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Trạng thái:</label>
                <select name="status" class="form-select">
                    <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Chưa làm</option>
                    <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                    <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                </select>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
            <hr>
            <div class="mb-3">
                <label class="form-label">Giao cho (Sở hữu):</label>
                <select name="owner_id" class="form-select">
                    
                    <option value="public" <?= ($task['user_id'] === null) ? 'selected' : '' ?>>
                        -- Công việc chung (Public) --
                    </option>
                    
                    <?php foreach($all_users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= ($task['user_id'] == $user['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                    <?php endforeach; ?>
                    
                </select>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between mt-4">
                <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>