<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'praziarenkavydb');
define('DB_CHARSET', 'utf8mb4');

define('DB_DSN', 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
?>