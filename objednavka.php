<?php
session_start();
require_once "db/config.php";

if(!isset($_SESSION['kosik']) || count($_SESSION['kosik']) == 0) {
    header("Location: kosik.php");
    exit;
}

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $_SESSION['redirect_after_login'] = 'objednavka.php';
    header("Location: prihlasenie.php");
    exit;
}

$ulica = $mesto = $psc = $telefon = $poznamka = $cislo = "";
$ulica_err = $mesto_err = $psc_err = $telefon_err = $cislo_err = "";
$platba = $dorucenie = "";
$platba_err = $dorucenie_err = "";

$sql = "SELECT * FROM pouzivatelia WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if($user_data = mysqli_fetch_assoc($result)) {
            $meno = $user_data['meno'] ?? "";
            $priezvisko = $user_data['priezvisko'] ?? "";
            $email = $user_data['email'] ?? "";
            $ulica = isset($user_data['ulica']) ? $user_data['ulica'] : "";
            $cislo = isset($user_data['cislo']) ? $user_data['cislo'] : "";
            $mesto = isset($user_data['mesto']) ? $user_data['mesto'] : "";
            $psc = isset($user_data['psc']) ? $user_data['psc'] : "";
            $telefon = isset($user_data['telefon']) ? $user_data['telefon'] : "";
        }
    }
    mysqli_stmt_close($stmt);
}

$celkova_suma = 0;
foreach($_SESSION['kosik'] as $item) {
    $celkova_suma += $item['cena'] * $item['mnozstvo'];
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["ulica"]))) {
        $ulica_err = "Zadajte adresu doručenia.";
    } else {
        $ulica = trim($_POST["ulica"]);
    }
    
    if(empty(trim($_POST["cislo"]))) {
        $cislo_err = "Zadajte číslo domu.";
    } else {
        $cislo = trim($_POST["cislo"]);
    }

    if(empty(trim($_POST["mesto"]))) {
        $mesto_err = "Zadajte mesto.";
    } else {
        $mesto = trim($_POST["mesto"]);
    }
    
    if(empty(trim($_POST["psc"]))) {
        $psc_err = "Zadajte PSČ.";
    } else {
        $psc = trim($_POST["psc"]);
    }
    
    if(empty(trim($_POST["telefon"]))) {
        $telefon_err = "Zadajte telefónne číslo.";
    } else {
        $telefon = trim($_POST["telefon"]);
    }
    
    if(empty($_POST["platba"])) {
        $platba_err = "Vyberte spôsob platby.";
    } else {
        $platba = $_POST["platba"];
    }
    
    if(empty($_POST["dorucenie"])) {
        $dorucenie_err = "Vyberte spôsob doručenia.";
    } else {
        $dorucenie = $_POST["dorucenie"];
    }
    
    $poznamka = trim($_POST["poznamka"]);
    
    if(empty($ulica_err) && empty($cislo_err) && empty($mesto_err) && empty($psc_err) && empty($telefon_err) && empty($platba_err) && empty($dorucenie_err)) {
        if(empty($user_data['ulica']) || empty($user_data['cilso']) || empty($user_data['mesto']) || empty($user_data['psc']) || empty($user_data['telefon'])) {
            $update_sql = "UPDATE pouzivatelia SET ulica = ?, cislo = ?, mesto = ?, psc = ?, telefon = ? WHERE id = ?";
            if($update_stmt = mysqli_prepare($conn, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, "sssssi", $ulica, $cislo, $mesto, $psc, $telefon, $_SESSION["id"]);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
            }
        }
        
        mysqli_begin_transaction($conn);
        
        try {
        $objednavka_sql = "INSERT INTO objednavky (pouzivatel_id, meno, priezvisko, email, celkova_suma, sposob_platby, sposob_dorucenia, ulica, cislo, mesto, psc, telefon, poznamka, stav) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Nová')";            
        if($objednavka_stmt = mysqli_prepare($conn, $objednavka_sql)) {
            mysqli_stmt_bind_param($objednavka_stmt, "isssdssssssss", $_SESSION["id"], $meno, $priezvisko, $email, $celkova_suma, $platba, $dorucenie, $ulica, $cislo, $mesto, $psc, $telefon, $poznamka);
            mysqli_stmt_execute($objednavka_stmt);
            $objednavka_id = mysqli_insert_id($conn); 
            mysqli_stmt_close($objednavka_stmt);
    
    $polozky_sql = "INSERT INTO objednavka_produkty (objednavka_id, produkt_id, mnozstvo, cena_za_kus, celkova_suma) VALUES (?, ?, ?, ?, ?)";
    if($polozky_stmt = mysqli_prepare($conn, $polozky_sql)) {
        foreach($_SESSION['kosik'] as $id => $item) {
            $produkt_id = $item['id']; 
            $cena_za_kus = $item['cena'];
            $mnozstvo = $item['mnozstvo'];
            $celkova_suma_polozky = $cena_za_kus * $mnozstvo;
            mysqli_stmt_bind_param($polozky_stmt, "iiidd", $objednavka_id, $produkt_id, $mnozstvo, $cena_za_kus, $celkova_suma_polozky);
            mysqli_stmt_execute($polozky_stmt);
            
            $update_stock_sql = "UPDATE produkty SET dostupne_mnozstvo = dostupne_mnozstvo - ? WHERE produkt_id = ?";
            if($update_stock_stmt = mysqli_prepare($conn, $update_stock_sql)) {
                mysqli_stmt_bind_param($update_stock_stmt, "ii", $mnozstvo, $produkt_id);
                mysqli_stmt_execute($update_stock_stmt);
                mysqli_stmt_close($update_stock_stmt);
            }
        }
        mysqli_stmt_close($polozky_stmt);
    }
                
                mysqli_commit($conn);
                
                $_SESSION['kosik'] = array();
                
                $_SESSION['objednavka_id'] = $objednavka_id;

                header("Location: thankyou.php");
                exit;
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Nastala chyba pri spracovaní objednávky: " . $e->getMessage();
        }
    }
}

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
            <div class="container" style="padding-top: 160px;" >
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
                                        <h6 class="text-white">Informácie o objednávateľovi</h6>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="meno" class="form-label">Meno</label>
                                                    <input type="text" id="meno" class="form-control bg-dark text-white" value="<?php echo $meno; ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="priezvisko" class="form-label">Priezvisko</label>
                                                    <input type="text" id="priezvisko" class="form-control bg-dark text-white" value="<?php echo $priezvisko; ?>">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" id="email" class="form-control bg-dark text-white" value="<?php echo $email; ?>">
                                            </div>
                                        </div>
        
                                    <hr class="my-4 border-secondary">
        
                                    <div class="mb-3">
                                        <h6 class="text-white">Dodacie údaje</h6>
                                        <label for="ulica" class="form-label">Ulica</label>
                                        <input type="text" name="ulica" id="ulica" class="form-control bg-dark text-white <?php echo (!empty($ulica_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ulica; ?>" required>
                                        <span class="invalid-feedback"><?php echo $ulica_err; ?></span>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="cislo" class="form-label">Číslo domu</label>
                                        <input type="text" name="cislo" id="cislo" class="form-control bg-dark text-white <?php echo (!empty($cislo_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cislo ?? ''; ?>" required>
                                        <span class="invalid-feedback"><?php echo $cislo_err ?? ''; ?></span>
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