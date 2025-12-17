<?php
require_once "db.php";

if (isset($_GET['id'])) {
    $db = new DbHelper();
    $db->execute("DELETE FROM users WHERE id = ?", [$_GET['id']]);
}

header("Location: listofusers.php");
exit;
