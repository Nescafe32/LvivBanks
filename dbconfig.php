<?php
$db_username = 'root';
$db_password = '';
$db_name = 'lviv_banks';
$db_host = 'localhost';

$db = new mysqli($db_host, $db_username, $db_password, $db_name);
$db->set_charset("utf8") or die("Can`t set charset");

if (mysqli_connect_errno()) {
    header('HTTP/1.1 500 Error: Could not connect to db!');
    exit();
}
?>