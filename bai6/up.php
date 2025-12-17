<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload file</title>
</head>
<body>

<h2>Upload file</h2>

<form method="post" enctype="multipart/form-data">
    Chọn file: <input type="file" name="file">
    <button type="submit" name="upload">Upload</button>
</form>

<?php
if (isset($_POST['upload'])) {
    $file = $_FILES['file'];

    if ($file['error'] == 0) {
        $name = $file['name'];
        move_uploaded_file($file['tmp_name'], "uploads/" . $name);
        echo "Upload thành công: $name";
    } else {
        echo "Upload thất bại";
    }
}
?>

</body>
</html>
