<?php
$file = "data.txt";

file_put_contents($file, "Hello PHP\n");

$content = file_get_contents($file);
echo nl2br($content);
