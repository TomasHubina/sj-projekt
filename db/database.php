<?php
require_once "config.php";

$sql_create_table = "CREATE TABLE IF NOT EXISTS pouzivatelia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meno VARCHAR(100) NOT NULL,
    priezvisko VARCHAR(100) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    heslo VARCHAR(255) NOT NULL,
    je_admin TINYINT(1) NOT NULL DEFAULT 0
)"; 

if (mysqli_query($conn, $sql_create_table)) {
    echo "Tabuľka pouzivatelia bola úspešne vytvorená.<br>";
} else {
    echo "Chyba pri vytváraní tabuľky pouzivatelia: " . mysqli_error($conn) . "<br>";
} 

$sql_create_produkty = "CREATE TABLE IF NOT EXISTS produkty (
    produkt_id INT AUTO_INCREMENT PRIMARY KEY,
    nazov VARCHAR(255) NOT NULL,
    popis TEXT,
    cena DECIMAL(10,2) NOT NULL,
    dostupne_mnozstvo INT NOT NULL DEFAULT 0,
    obrazok VARCHAR(255)
)";

if (mysqli_query($conn, $sql_create_produkty)) {
    echo "Tabuľka produkty bola úspešne vytvorená.<br>";
} else {
    echo "Chyba pri vytváraní tabuľky produkty: " . mysqli_error($conn) . "<br>";
}

$sql_create_objednavky = "CREATE TABLE IF NOT EXISTS objednavky (
    objednavka_id INT AUTO_INCREMENT PRIMARY KEY,
    pouzivatel_id INT NOT NULL,
    meno VARCHAR(100) NOT NULL,
    priezvisko VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    ulica VARCHAR(50) NOT NULL,
    cislo VARCHAR(10) NOT NULL,
    mesto VARCHAR(100) NOT NULL,
    psc VARCHAR(10) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    celkova_suma DECIMAL(10,2) NOT NULL,
    stav VARCHAR(50) NOT NULL DEFAULT 'Nová',
    sposob_platby VARCHAR(50) NOT NULL,
    sposob_dorucenia VARCHAR(50) NOT NULL,
    poznamka TEXT,
    FOREIGN KEY (pouzivatel_id) REFERENCES pouzivatelia(id)
)";

if (mysqli_query($conn, $sql_create_objednavky)) {
    echo "Tabuľka objednavky bola úspešne vytvorená.<br>";
} else {
    echo "Chyba pri vytváraní tabuľky objednavky: " . mysqli_error($conn) . "<br>";
}

$sql_create_objednavka_produkty = "CREATE TABLE IF NOT EXISTS objednavka_produkty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objednavka_id INT NOT NULL,
    produkt_id INT NOT NULL,
    mnozstvo INT NOT NULL,
    cena_za_kus DECIMAL(10,2) NOT NULL,
    celkova_suma DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (objednavka_id) REFERENCES objednavky(objednavka_id),
    FOREIGN KEY (produkt_id) REFERENCES produkty(produkt_id)
)";

if (mysqli_query($conn, $sql_create_objednavka_produkty)) {
    echo "Tabuľka objednavka_produkty bola úspešne vytvorená.<br>";
} else {
    echo "Chyba pri vytváraní tabuľky objednavka_produkty: " . mysqli_error($conn) . "<br>";
}

$admin_heslo = password_hash("admin123", PASSWORD_DEFAULT);

$sql_check_admin = "SELECT id FROM pouzivatelia WHERE meno = 'Admin' AND je_admin = 1";
$result = mysqli_query($conn, $sql_check_admin);

if (mysqli_num_rows($result) == 0) {
    $sql_insert_admin = "INSERT INTO pouzivatelia (meno, email, heslo, je_admin) 
                         VALUES ('Admin', 'admin@a.sk', '$admin_heslo', 1)";
    
    if (mysqli_query($conn, $sql_insert_admin)) {
        echo "Admin používateľ bol úspešne vytvorený.<br>";
    } else {
        echo "Chyba pri vytváraní admin používateľa: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Admin používateľ už existuje.<br>";
}

// Overenie stĺpcov v tabuľke (pre diagnostiku)
$result = mysqli_query($conn, "SHOW TABLES");
echo "<h3>Tabuľky v databáze:</h3>";
echo "<pre>";
while ($row = mysqli_fetch_row($result)) {
    echo $row[0] . "\n";
}
echo "</pre>";

mysqli_close($conn);
echo "Inštalácia databázy dokončená!";
?>