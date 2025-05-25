<?php
session_start();
require_once "db/config.php";

// Kontrola, či je košík prázdny
if(!isset($_SESSION['kosik']) || count($_SESSION['kosik']) == 0) {
    header("Location: kosik.php");
    exit;
}

// Kontrola, či je používateľ prihlásený
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['redirect_after_login'] = 'objednavka.php';
    header("Location: prihlasenie.php");
    exit;
}

// Inicializácia premenných
$adresa = $mesto = $psc = $telefon = $poznamka = "";
$adresa_err = $mesto_err = $psc_err = $telefon_err = "";
$platba = $dorucenie = "";
$platba_err = $dorucenie_err = "";

// Načítanie údajov používateľa z databázy
$sql = "SELECT * FROM pouzivatelia WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($user_data = mysqli_fetch_assoc($result)) {
            // Fix for missing user data fields
            $adresa = isset($user_data['adresa']) ? $user_data['adresa'] : "";
            $mesto = isset($user_data['mesto']) ? $user_data['mesto'] : "";
            $psc = isset($user_data['psc']) ? $user_data['psc'] : "";
            $telefon = isset($user_data['telefon']) ? $user_data['telefon'] : "";
            $meno = $user_data['meno'] ?? ""; // Make sure these fields exist
            $priezvisko = $user_data['priezvisko'] ?? "";
        }
    }
    mysqli_stmt_close($stmt);
}

// Výpočet celkovej sumy
$celkova_suma = 0;
foreach($_SESSION['kosik'] as $item) {
    $celkova_suma += $item['cena'] * $item['mnozstvo'];
}

// Spracovanie objednávky
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validácia adresy
    if(empty(trim($_POST["adresa"]))) {
        $adresa_err = "Zadajte adresu doručenia.";
    } else {
        $adresa = trim($_POST["adresa"]);
    }
    
    // Validácia mesta
    if(empty(trim($_POST["mesto"]))) {
        $mesto_err = "Zadajte mesto.";
    } else {
        $mesto = trim($_POST["mesto"]);
    }
    
    // Validácia PSČ
    if(empty(trim($_POST["psc"]))) {
        $psc_err = "Zadajte PSČ.";
    } else {
        $psc = trim($_POST["psc"]);
    }
    
    // Validácia telefónu
    if(empty(trim($_POST["telefon"]))) {
        $telefon_err = "Zadajte telefónne číslo.";
    } else {
        $telefon = trim($_POST["telefon"]);
    }
    
    // Validácia spôsobu platby
    if(empty($_POST["platba"])) {
        $platba_err = "Vyberte spôsob platby.";
    } else {
        $platba = $_POST["platba"];
    }
    
    // Validácia spôsobu doručenia
    if(empty($_POST["dorucenie"])) {
        $dorucenie_err = "Vyberte spôsob doručenia.";
    } else {
        $dorucenie = $_POST["dorucenie"];
    }
    
    // Poznámka (nepovinná)
    $poznamka = trim($_POST["poznamka"]);
    
    // Kontrola chýb pred vložením do databázy
    if(empty($adresa_err) && empty($mesto_err) && empty($psc_err) && empty($telefon_err) && empty($platba_err) && empty($dorucenie_err)) {
        // Uloženie adresy používateľa, ak nie je vyplnená
        if(empty($user_data['adresa']) || empty($user_data['mesto']) || empty($user_data['psc']) || empty($user_data['telefon'])) {
            $update_sql = "UPDATE pouzivatelia SET adresa = ?, mesto = ?, psc = ?, telefon = ? WHERE id = ?";
            if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "ssssi", $adresa, $mesto, $psc, $telefon, $_SESSION["id"]);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            }
        }
        
        // Začatie transakcie
        mysqli_begin_transaction($conn);
        
        try {
            // Vytvorenie objednávky
            // Vytvorenie objednávky - upravený SQL dotaz s objednavka_id namiesto id
$objednavka_sql = "INSERT INTO objednavky (pouzivatel_id, meno, priezvisko, celkova_suma, sposob_platby, sposob_dorucenia, adresa, mesto, psc, telefon, poznamka, stav) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nová')";            
if($objednavka_stmt = mysqli_prepare($conn, $objednavka_sql)) {
    mysqli_stmt_bind_param($objednavka_stmt, "issdsssssss", $_SESSION["id"], $meno, $priezvisko, $celkova_suma, $platba, $dorucenie, $adresa, $mesto, $psc, $telefon, $poznamka);
    mysqli_stmt_execute($objednavka_stmt);
    $objednavka_id = mysqli_insert_id($conn); // Toto by malo stále fungovať
    mysqli_stmt_close($objednavka_stmt);
    
    // Vloženie položiek objednávky
    $polozky_sql = "INSERT INTO objednavka_produkty (objednavka_id, produkt_id, mnozstvo, cena_za_kus) VALUES (?, ?, ?, ?)";
    if($polozky_stmt = mysqli_prepare($conn, $polozky_sql)) {
        foreach($_SESSION['kosik'] as $id => $item) {
            // Uprav toto - použij produkt_id namiesto id ak je to tak v databáze
            $produkt_id = $item['id']; // Toto by malo byť produkt_id v závislosti od štruktúry košíka
            mysqli_stmt_bind_param($polozky_stmt, "iiid", $objednavka_id, $produkt_id, $item['mnozstvo'], $item['cena']);
            mysqli_stmt_execute($polozky_stmt);
            
            // Aktualizácia stavu skladu - uprav WHERE podmienku na produkt_id
            $update_stock_sql = "UPDATE produkty SET dostupne_mnozstvo = dostupne_mnozstvo - ? WHERE produkt_id = ?";
            if($update_stock_stmt = mysqli_prepare($conn, $update_stock_sql)) {
                mysqli_stmt_bind_param($update_stock_stmt, "ii", $item['mnozstvo'], $produkt_id);
                mysqli_stmt_execute($update_stock_stmt);
                mysqli_stmt_close($update_stock_stmt);
            }
        }
        mysqli_stmt_close($polozky_stmt);
    }
                
                // Commit transakcie
                mysqli_commit($conn);
                
                // Vyprázdnenie košíka
                $_SESSION['kosik'] = array();
                
                // Presmerovanie na stránku s potvrdením
                header("Location: moje-objednavky.php?success=1&id=".$objednavka_id);
                exit;
            }
        } catch (Exception $e) {
            // Rollback v prípade chyby
            mysqli_rollback($conn);
            echo "Nastala chyba pri spracovaní objednávky: " . $e->getMessage();
        }
    }
}

// Zatvorenie spojenia
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="sk">
<?php require_once "parts/head.php"; ?>
<body>
    
    
    <main>
        <section class="about-section" id="section_objednavka">
            <?php require_once "parts/nav.php"; ?>
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row" >
                    <div class="col-12">
                        <em class="text-white">Finalizácia</em>
                        <h2 class="text-white mb-4">Dokončenie objednávky</h2>
                    </div>

                    <div class="col-md-8">
                        <div class="card bg-dark text-white mb-4">
                            <div class="card-header">
                                <h5 class="mb-0 text-white">Dodacie údaje</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="mb-3">
                                        <label for="adresa" class="form-label">Adresa</label>
                                        <input type="text" name="adresa" id="adresa" class="form-control bg-dark text-white <?php echo (!empty($adresa_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $adresa; ?>" required>
                                        <span class="invalid-feedback"><?php echo $adresa_err; ?></span>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="mesto" class="form-label">Mesto</label>
                                            <input type="text" name="mesto" id="mesto" class="form-control bg-dark text-white <?php echo (!empty($mesto_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $mesto; ?>" required>
                                            <span class="invalid-feedback"><?php echo $mesto_err; ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="psc" class="form-label">PSČ</label>
                                            <input type="text" name="psc" id="psc" class="form-control bg-dark text-white <?php echo (!empty($psc_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $psc; ?>" required>
                                            <span class="invalid-feedback"><?php echo $psc_err; ?></span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="telefon" class="form-label">Telefón</label>
                                        <input type="tel" name="telefon" id="telefon" class="form-control bg-dark text-white <?php echo (!empty($telefon_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $telefon; ?>" required>
                                        <span class="invalid-feedback"><?php echo $telefon_err; ?></span>
                                    </div>
                                    
                                    <hr class="my-4 border-secondary">
                                    
                                    <h5 class="text-white">Spôsob doručenia</h5>
                                    <div class="mb-3 <?php echo (!empty($dorucenie_err)) ? 'is-invalid' : ''; ?>">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="dorucenie" id="dorucenie1" value="Kuriér" <?php echo ($dorucenie == "Kuriér") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="dorucenie1">
                                                Kuriér (4,90 €)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="dorucenie" id="dorucenie2" value="Pošta" <?php echo ($dorucenie == "Pošta") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="dorucenie2">
                                                Slovenská pošta (3,50 €)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="dorucenie" id="dorucenie3" value="Osobný odber" <?php echo ($dorucenie == "Osobný odber") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="dorucenie3">
                                                Osobný odber v predajni (0 €)
                                            </label>
                                        </div>
                                        <span class="invalid-feedback"><?php echo $dorucenie_err; ?></span>
                                    </div>
                                    
                                    <hr class="my-4 border-secondary">
                                    
                                    <h5 class="text-white">Spôsob platby</h5>
                                    <div class="mb-3 <?php echo (!empty($platba_err)) ? 'is-invalid' : ''; ?>">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="platba" id="platba1" value="Dobierka" <?php echo ($platba == "Dobierka") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="platba1">
                                                Dobierka
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="platba" id="platba2" value="Bankový prevod" <?php echo ($platba == "Bankový prevod") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="platba2">
                                                Bankový prevod
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="platba" id="platba3" value="Platobná karta" <?php echo ($platba == "Platobná karta") ? "checked" : ""; ?>>
                                            <label class="form-check-label" for="platba3">
                                                Platobná karta online
                                            </label>
                                        </div>
                                        <span class="invalid-feedback"><?php echo $platba_err; ?></span>
                                    </div>

                                    <hr class="my-4 border-secondary">
                                    
                                    <div class="mb-3">
                                        <label for="poznamka" class="form-label">Poznámka k objednávke (nepovinné)</label>
                                        <textarea name="poznamka" id="poznamka" class="form-control bg-dark text-white" rows="3"><?php echo $poznamka; ?></textarea>
                                    </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-dark text-white mb-4">
                            <div class="card-header">
                                <h5 class="mb-0 text-white">Objednávka</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach($_SESSION['kosik'] as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between lh-sm bg-dark text-white border-secondary">
                                            <div>
                                                <h6 class="my-0"><?php echo htmlspecialchars($item['nazov']); ?></h6>
                                                <small class="text-light"><?php echo $item['mnozstvo']; ?> ks × <?php echo number_format($item['cena'], 2); ?> €</small>
                                            </div>
                                            <span class="text-light"><?php echo number_format($item['cena'] * $item['mnozstvo'], 2); ?> €</span>
                                        </li>
                                    <?php endforeach; ?>

                                    <li class="list-group-item d-flex justify-content-between bg-dark text-white border-secondary">
                                        <span>Spolu</span>
                                        <strong><?php echo number_format($celkova_suma, 2); ?> €</strong>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn custom-btn w-100">
                                    <i class="bi bi-check-circle"></i> Objednať s povinnosťou platby
                                </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="kosik.php" class="text-white">
                                <i class="bi bi-arrow-left"></i> Späť do košíka
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php require_once "parts/footer.php"; ?>
</body>
</html>