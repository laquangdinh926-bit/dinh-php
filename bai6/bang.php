<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo bảng PHP</title>
</head>
<body>

<h2>Nhập số dòng và số cột</h2>

<form method="post">
    Số dòng: <input type="number" name="dong" required><br><br>
    Số cột: <input type="number" name="cot" required><br><br>
    <button type="submit">Tạo bảng</button>
</form>

<?php
if (isset($_POST['dong']) && isset($_POST['cot'])) {
    $dong = $_POST['dong'];
    $cot = $_POST['cot'];

    echo "<h3>Bảng $dong x $cot</h3>";
    echo "<table border='1' cellpadding='10'>";
    for ($i = 1; $i <= $dong; $i++) {
        echo "<tr>";
        for ($j = 1; $j <= $cot; $j++) {
            echo "<td>D$i-C$j</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>

</body>
</html>
