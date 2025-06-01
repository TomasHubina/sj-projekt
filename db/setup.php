<?php
require_once 'database.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Vytváram tabuľky databázy...</h2>";
    
    if ($db->createTables()) {
        echo "Tabuľky boli úspešne vytvorené.<br>";
    }
    
    if ($db->createAdminUser()) {
        echo "Admin používateľ bol úspešne vytvorený.<br>";
    } else {
        echo "Admin používateľ už existuje.<br>";
    }
    
    echo "<h3>Tabuľky v databáze:</h3>";
    echo "<pre>";
    $tables = $db->fetchAll("SHOW TABLES");
    foreach ($tables as $table) {
        echo $table[array_key_first($table)] . "\n";
    }
    echo "</pre>";
    
    echo "<p>Inštalácia databázy bola úspešne dokončená!</p>";
    
} catch (Exception $e) {
    echo "<h2>Chyba pri inštalácii databázy!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>