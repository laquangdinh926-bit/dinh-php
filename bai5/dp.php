<?php
$conn = new mysqli("localhost", "root", "", "test");

if ($conn->connect_error) {
    die("Kết nối thất bại");
}

echo "Kết nối CSDL thành công";
