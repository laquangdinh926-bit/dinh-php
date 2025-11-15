<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Thông báo
$msg = $_GET['msg'] ?? '';
$all_users = []; // Khởi tạo mảng user

// --- LẤY DANH SÁCH USER CHO ADMIN ---
// (Cần cho cả form Thêm mới và Bảng)
if ($_SESSION['role'] === 'admin') {
    $stmt_users = $conn->prepare("SELECT id, username FROM users ORDER BY username ASC");
    $stmt_users->execute();
    $all_users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
}
// --- KẾT THÚC LẤY USER ---


// Thêm công việc mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'] ?: null;

    // --- LOGIC THÊM MỚI (ĐÃ SỬA) ---
    // Mặc định task là của cá nhân người tạo (cho User thường)
    $task_user_id = $_SESSION['user_id']; 
    
    // Nếu là Admin, kiểm tra dropdown "Giao cho"
    if ($_SESSION['role'] === 'admin' && isset($_POST['owner_id'])) {
        $owner_id = $_POST['owner_id'];
        
        if ($owner_id === 'public') {
            $task_user_id = null; // Công việc chung
        } else {
            $task_user_id = (int)$owner_id; // Gán cho user cụ thể
        }
    }
    // --- KẾT THÚC SỬA ---

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (:user_id, :title, :description, :due_date)");
    $stmt->execute([
        ':user_id' => $task_user_id, // Dùng biến mới
        ':title' => $title,
        ':description' => $description,
        ':due_date' => $due_date
    ]);
    header("Location: dashboard.php?msg=added");
    exit;
}

// LẤY DỮ LIỆU TỪ FORM (Filter và Search)
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// --- LOGIC LẤY DỮ LIỆU (Giữ nguyên logic cũ) ---
$base_query = "
    SELECT tasks.*, users.username 
    FROM tasks 
    LEFT JOIN users ON tasks.user_id = users.id
";
$params = [];

if ($_SESSION['role'] === 'admin') {
    $query = $base_query . " WHERE 1=1"; 
} else {
    $query = $base_query . " WHERE (tasks.user_id = :user_id OR tasks.user_id IS NULL)";
    $params[':user_id'] = $_SESSION['user_id'];
}
// --- KẾT THÚC LẤY ---


// Thêm điều kiện lọc STATUS
if ($statusFilter) {
    $query .= " AND tasks.status = :status";
    $params[':status'] = $statusFilter;
}

// Thêm điều kiện TÌM KIẾM
if ($search) {
    $query .= " AND (tasks.title LIKE :search OR tasks.description LIKE :search)";
    $params[':search'] = "%$search%"; 
}

$query .= " ORDER BY tasks.due_date ASC";

// Thực thi query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Quản lý công việc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <h2>Chào, <?=htmlspecialchars($_SESSION['username'])?>!</h2>
        <div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin_users.php" class="btn btn-info">
                    <i class="bi bi-people-fill"></i> Quản lý tài khoản
                </a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-danger">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </a>
        </div>
    </div>

    <?php if($msg === 'added'): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> Thêm công việc thành công!</div>
    <?php elseif($msg === 'updated'): ?>
        <div class="alert alert-success"><i class="bi bi-pencil-fill"></i> Cập nhật công việc thành công!</div>
    <?php elseif($msg === 'deleted'): ?>
        <div class="alert alert-success"><i class="bi bi-trash-fill"></i> Xóa công việc thành công!</div>
    <?php elseif($msg === 'notfound'): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> Công việc không tồn tại!</div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <h4 class="mb-3">Thêm công việc mới</h4>
        <input type="hidden" name="add_task" value="1">
        <div class="mb-3">
            <input type="text" name="title" placeholder="Tiêu đề" class="form-control" required>
        </div>
        <div class="mb-3">
            <textarea name="description" placeholder="Mô tả (tùy chọn)" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <input type="date" name="due_date" class="form-control">
        </div>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="mb-3">
            <label class="form-label">Giao cho (Sở hữu):</label>
            <select name="owner_id" class="form-select">
                
                <option value="public">
                    -- Công việc chung (Public) --
                </option>
                
                <?php foreach($all_users as $user): ?>
                <option value="<?= $user['id'] ?>">
                    <?= htmlspecialchars($user['username']) ?>
                </option>
                <?php endforeach; ?>
                
            </select>
        </div>
        <?php endif; ?>
        <button class="btn btn-success"><i class="bi bi-plus-lg"></i> Thêm công việc</button>
    </form>

    <form method="get" class="mb-3 row g-3 p-4 bg-white" style="border-radius: 12px;">
         <h4 class="mb-0">Danh sách công việc</h4>
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" 
                   placeholder="Tìm theo tiêu đề hoặc mô tả..." 
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" <?=($statusFilter=='pending')?'selected':''?>>Chưa làm</option>
                <option value="in_progress" <?=($statusFilter=='in_progress')?'selected':''?>>Đang làm</option>
                <option value="completed" <?=($statusFilter=='completed')?'selected':''?>>Hoàn thành</option>
            </select>
        </div>
        <div class="col-md-3 d-flex">
            <button class="btn btn-primary me-2"><i class="bi bi-funnel-fill"></i> Lọc/Tìm</button>
            <a href="dashboard.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Mô tả</th>
                    <th>Hạn</th>
                    <th>Trạng thái</th>
                    <th>Sở hữu</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task): ?>
                    <tr>
                        <td><?=htmlspecialchars($task['title'])?></td>
                        <td><?=htmlspecialchars($task['description'])?></td>
                        <td><?=htmlspecialchars($task['due_date'])?></td>
                        <td>
                            <?php if($task['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark">Chưa làm</span>
                            <?php elseif($task['status'] == 'in_progress'): ?>
                                <span class="badge bg-info text-dark">Đang làm</span>
                            <?php else: ?>
                                <span class="badge bg-success">Hoàn thành</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <?php
                            if ($task['user_id'] === null) {
                                echo '<span class="badge bg-info text-dark">Công việc chung</span>';
                            } else {
                                echo htmlspecialchars($task['username']);
                            }
                            ?>
                        </td>
                        
                        <td>
                            <a href="edit_task.php?id=<?=$task['id']?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="delete_task.php?id=<?=$task['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xoá?')">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($tasks) == 0): ?>
                    <tr><td colspan="6" class="text-center p-4">
                        <?php if($search || $statusFilter): ?>
                            Không tìm thấy công việc nào phù hợp.
                        <?php else: ?>
                            Bạn chưa có công việc nào. Hãy thêm một công việc mới!
                        <?php endif; ?>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>