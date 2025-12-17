<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đếm chữ cái trong tên</title>
</head>
<body>

<h2>Nhập họ tên</h2>

<form method="post">
    Họ tên: 
    <input type="text" name="hoten" required>
    <button type="submit">Đếm</button>
</form>

<?php
if (isset($_POST['hoten'])) {
    $hoten = trim($_POST['hoten']);

    // Đếm chữ cái (không tính khoảng trắng)
    $khongKhoangTrang = str_replace(" ", "", $hoten);
    $soChuCai = mb_strlen($khongKhoangTrang, "UTF-8");

    echo "<p>Họ tên: <b>$hoten</b></p>";
    echo "<p>Số chữ cái: <b>$soChuCai</b></p>";
}
?>

</body>
</html>
