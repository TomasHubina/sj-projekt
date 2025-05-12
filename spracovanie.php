<?php
// Kontrola prístupu a metódy
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: objednavka.php");
    exit;
}

// Pripojenie konfiguračného súboru
require_once "includes/config.php";

// Kontrola odoslaných údajov
if(!isset($_POST['meno']) || !isset($_POST['priezvisko']) || !isset($_POST['email']) || !isset($_POST['telefon']) || 
   !isset($_POST['adresa']) || !isset($_POST['mesto']) || !isset($_POST['psc']) || !isset($_POST['mnozstvo'])) {
    die("Chýbajúce povinné údaje.");
}

// Príprava údajov z formulára
$meno = mysqli_real_escape_string($conn, $_POST['meno']);
$priezvisko = mysqli_real_escape_string($conn, $_POST['priezvisko']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$telefon = mysqli_real_escape_string($conn, $_POST['telefon']);
$adresa = mysqli_real_escape_string($conn, $_POST['adresa']);
$mesto = mysqli_real_escape_string($conn, $_POST['mesto']);
$psc = mysqli_real_escape_string($conn, $_POST['psc']);
$poznamka = isset($_POST['poznamka']) ? mysqli_real_escape_string($conn, $_POST['poznamka']) : '';

// Získanie produktov a výpočet celkovej sumy
$celkova_suma = 0;
$polozky = [];

foreach($_POST['mnozstvo'] as $produkt_id => $mnozstvo) {
    $produkt_id = (int)$produkt_id;
    $mnozstvo = (int)$mnozstvo;
    
    if($mnozstvo > 0) {
        // Získanie informácií o produkte
        $sql = "SELECT * FROM produkty WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $produkt_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            // Kontrola dostupnosti
            if($row['dostupne_mnozstvo'] >= $mnozstvo) {
                $cena_polozky = $row['cena'] * $mnozstvo;
                $celkova_suma += $cena_polozky;
                
                $polozky[] = [
                    'produkt_id' => $produkt_id,
                    'mnozstvo' => $mnozstvo,
                    'cena' => $row['cena']
                ];
                
                // Aktualizácia dostupného množstva
                $nove_mnozstvo = $row['dostupne_mnozstvo'] - $mnozstvo;
                $update_sql = "UPDATE produkty SET dostupne_mnozstvo = ? WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "ii", $nove_mnozstvo, $produkt_id);
                mysqli_stmt_execute($update_stmt);
            }
        }
    }
}

// Ak nie sú žiadne položky, presmerovanie späť
if(empty($polozky)) {
    header("Location: objednavka.php?error=no_items");
    exit;
}

// Vloženie objednávky do databázy
$sql = "INSERT INTO objednavky (meno, priezvisko, email, telefon, adresa, mesto, psc, poznamka, celkova_suma) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssssssd", $meno, $priezvisko, $email, $telefon, $adresa, $mesto, $psc, $poznamka, $celkova_suma);

if(mysqli_stmt_execute($stmt)) {
    $objednavka_id = mysqli_insert_id($conn);
    
    // Vloženie položiek objednávky
    $sql = "INSERT INTO polozky_objednavky (objednavka_id, produkt_id, mnozstvo, cena) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    foreach($polozky as $polozka) {
        mysqli_stmt_bind_param($stmt, "iiid", $objednavka_id, $polozka['produkt_id'], $polozka['mnozstvo'], $polozka['cena']);
        mysqli_stmt_execute($stmt);
    }
    
    // Presmerovanie na stránku s poďakovaním
    header("Location: thankyou.php?id=" . $objednavka_id);
} else {
    die("Chyba pri vytváraní objednávky: " . mysqli_error($conn));
}
?>