<?php
class DbHelper {
    private $conn;

    public function __construct() {
        // Đổi 'bt1' → 'test'
        $this->conn = new mysqli("localhost", "root", "", "test");
        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
    }

    public function select($sql) {
        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function selectOne($sql) {
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
}
