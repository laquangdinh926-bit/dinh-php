<?php
require_once "db.php";

$db = new DbHelper();
$id = $_GET['id'];

$user = $db->select("SELECT * FROM users WHERE id = ? LIMIT 1", [$id], false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $db->execute("UPDATE users SET username = ?, password = ? WHERE id = ?", [$username, $password, $id]);
    header("Location: listofusers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa tài khoản</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-primary">✏ Sửa tài khoản</h2>
    <form method="POST" class="card p-3 shadow mt-3">
        <label>Username:</label>
        <input type="text" name="username" class="form-control" value="<?php echo $user->username; ?>" required>

        <label class="mt-3">Password:</label>
        <input type="text" name="password" class="form-control" value="<?php echo $user->password; ?>" required>

        <button class="btn btn-success mt-3">Lưu thay đổi</button>
        <a href="listofusers.php" class="btn btn-secondary mt-3">Quay lại</a>
    </form>
</div>

</body>
</html>
