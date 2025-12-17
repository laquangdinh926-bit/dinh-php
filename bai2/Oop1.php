<?php
// Khai báo class
class SinhVien {
    // Thuộc tính
    public $maSV;
    public $tenSV;
    public $tuoi;

    // Hàm khởi tạo
    public function __construct($maSV, $tenSV, $tuoi) {
        $this->maSV = $maSV;
        $this->tenSV = $tenSV;
        $this->tuoi = $tuoi;
    }

    // Phương thức hiển thị thông tin
    public function hienThiThongTin() {
        echo "Mã sinh viên: " . $this->maSV . "<br>";
        echo "Tên sinh viên: " . $this->tenSV . "<br>";
        echo "Tuổi: " . $this->tuoi . "<br>";
    }
}

// Tạo đối tượng
$sv1 = new SinhVien("SV01", "Nguyễn Văn A", 20);

echo "<h3>Thông tin sinh viên</h3>";
$sv1->hienThiThongTin();
?>
