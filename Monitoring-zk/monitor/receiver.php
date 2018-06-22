<?php
include 'inc/init.php';
$monitor = new Monitor();
$monitor->insertDataFromZipfile($_FILES["file_contents"]["tmp_name"]);
?>
