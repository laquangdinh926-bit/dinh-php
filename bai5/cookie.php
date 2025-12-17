<?php
setcookie("username", "Mhiu", time() + 3600);

if (isset($_COOKIE["username"])) {
    echo "Xin chào " . $_COOKIE["username"];
} else {
    echo "Chưa có cookie";
}
