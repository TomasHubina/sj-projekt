<?php
require_once "config.php";

// SQL príkaz na vytvorenie tabuľky pouzivatelia s existujúcou štruktúrou
$sql_create_table = "CREATE TABLE IF NOT EXISTS pouzivatelia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meno VARCHAR(100) NOT NULL,
    heslo VARCHAR(255) NOT NULL,
    je_admin TINYINT(1) NOT NULL DEFAULT 0,
    datum_vytvorenia DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql_create_table)) {
    echo "Tabuľka pouzivatelia bola úspešne vytvorená.<br>";
} else {
    echo "Chyba pri vytváraní tabuľky pouzivatelia: " . mysqli_error($conn) . "<br>";
}

// Vloženie testovacieho admin používateľa (heslo: admin123)
$admin_heslo = password_hash("admin123", PASSWORD_DEFAULT);

// Kontrolujeme, či admin existuje podľa mena (nie emailu)
$sql_check_admin = "SELECT id FROM pouzivatelia WHERE meno = 'Admin' AND je_admin = 1";
$result = mysqli_query($conn, $sql_check_admin);

if (mysqli_num_rows($result) == 0) {
    // Vkladáme len existujúce stĺpce
    $sql_insert_admin = "INSERT INTO pouzivatelia (meno, heslo, je_admin) 
                         VALUES ('Admin', '$admin_heslo', 1)";
    
    if (mysqli_query($conn, $sql_insert_admin)) {
        echo "Admin používateľ bol úspešne vytvorený.<br>";
    } else {
        echo "Chyba pri vytváraní admin používateľa: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Admin používateľ už existuje.<br>";
}

// Overenie stĺpcov v tabuľke (pre diagnostiku)
$result = mysqli_query($conn, "DESCRIBE pouzivatelia");
echo "<h3>Štruktúra tabuľky pouzivatelia:</h3>";
echo "<pre>";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

mysqli_close($conn);
echo "Inštalácia databázy dokončená!";
?>