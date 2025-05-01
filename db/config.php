<?php
// Konfigurácia pripojenia do databázy 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'praziarenkavydb');

// Pokus o pripojenie do databázy
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kontrola spojenia
if ($conn === false) {
    die("CHYBA: Nepodarilo sa pripojiť do databázy. " . mysqli_connect_error());
}

// Nastavenie kódovania
mysqli_set_charset($conn, "utf8mb4");
?>