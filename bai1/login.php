<?php
session_start();
require_once "db.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $db = new DbHelper();

    // Truy vấn đúng cột username
    $user = $db->select("SELECT * FROM users WHERE username = ? LIMIT 1", [$username], false);

    if ($user && $user->password === $password) {

        $_SESSION['logged'] = $user->username;
        header("Location: index.php");
        exit;
    } else {
        $message = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>
<body>

<?php if ($message): ?>
    <p style="color:red"><?= $message ?></p>
<?php endif; ?>

<h2>Login Form</h2>
<form method="POST" action="">
    <label>Username:</label>
    <input type="text" name="username" required> <br><br>

    <label>Password:</label>
    <input type="password" name="password" required> <br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
