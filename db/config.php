<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'praziarenkavydb');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn === false) {
    die("CHYBA: Nepodarilo sa pripojiť do databázy. " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>