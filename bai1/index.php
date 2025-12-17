<?php
session_start();

// Nếu chưa đăng nhập → quay lại login
if (!isset($_SESSION['logged'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trang chính</title>
</head>
<body>

<h2>Xin chào, <?php echo htmlspecialchars($_SESSION['logged']); ?>!</h2>

<p><a href="listofusers.php">Xem danh sách người dùng</a></p>
<p><a href="logout.php">Đăng xuất</a></p>

<hr>

<?php
echo "Server: " . $_SERVER['PHP_SELF'] . "<br>";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "Host: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "Script: " . $_SERVER['SCRIPT_NAME'] . "<br>";

echo htmlspecialchars('<h2>This text is in h2 heading format & a <= b </h2>');
?>

</body>
</html>
