<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php"); 
    exit;
}

require_once "../db/config.php";
require_once "../db/model/Pouzivatel.php";

$meno = $priezvisko = $email = $heslo = $heslo_potvrdenie = "";
$meno_err = $priezvisko_err = $email_err = $heslo_err = $heslo_potvrdenie_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["meno"]))){
        $meno_err = "Zadajte vaše meno.";
    } else{
        $meno = trim($_POST["meno"]);
    }
    
    if(empty(trim($_POST["priezvisko"]))){
        $priezvisko_err = "Zadajte vaše priezvisko.";     
    } else{
        $priezvisko = trim($_POST["priezvisko"]);
    }

    if(empty(trim($_POST["email"]))){
        $email_err = "Zadajte váš email.";     
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Zadajte platný email.";
    } else{
        $existujuci_pouzivatel = Pouzivatel::findByEmail(trim($_POST["email"]));
        
        if($existujuci_pouzivatel){
            $email_err = "Tento email je už použitý.";
        } else{
            $email = trim($_POST["email"]);
        }
    }

    if(empty(trim($_POST["heslo"]))){
        $heslo_err = "Zadajte heslo.";     
    } elseif(strlen(trim($_POST["heslo"])) < 6){
        $heslo_err = "Heslo musí mať aspoň 6 znakov.";
    } else{
        $heslo = trim($_POST["heslo"]);
    }
    
    if(empty(trim($_POST["heslo_potvrdenie"]))){
        $heslo_potvrdenie_err = "Potvrďte heslo.";     
    } else{
        $heslo_potvrdenie = trim($_POST["heslo_potvrdenie"]);
        if(empty($heslo_err) && ($heslo != $heslo_potvrdenie)){
            $heslo_potvrdenie_err = "Heslá sa nezhodujú.";
        }
    }
    
    if(empty($meno_err) && empty($priezvisko_err) && empty($email_err) && empty($heslo_err) && empty($heslo_potvrdenie_err)){         
            try {
            // Vytvorenie nového používateľa pomocou OOP
            $novy_pouzivatel = new Pouzivatel();
            $novy_pouzivatel->setMeno($meno);
            $novy_pouzivatel->setPriezvisko($priezvisko);
            $novy_pouzivatel->setEmail($email);
            $novy_pouzivatel->setPassword($heslo); // Táto metóda už zahŕňa password_hash()
            
            // Uloženie používateľa do databázy
            $id = $novy_pouzivatel->save();
            
            if($id){
                // Úspešná registrácia, presmerovanie na prihlásenie
                header("location: prihlasenie.php");
                exit;
            } else{
                echo "Ups! Niečo sa pokazilo pri ukladaní používateľa. Skúste to neskôr.";
            }
        } catch (Exception $e) {
            echo "Nastala chyba: " . $e->getMessage();
        }
    }
}

?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrácia - Gold Coffee</title>
    
    <!-- CSS -->                
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/vegas.min.css" rel="stylesheet">
    <link href="../css/tooplate-barista.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card bg-dark text-white">
                    <div class="card-header text-center">
                        <h2 class="text-white">Registrácia</h2>
                        <a href="../index.php" class="text-white text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Späť na hlavnú stránku
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="meno" class="form-label">Meno</label>
                                <input type="text" name="meno" class="form-control <?php echo (!empty($meno_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $meno; ?>" id="meno" required>
                                <span class="invalid-feedback"><?php echo $meno_err; ?></span>
                            </div>

                            <div class="mb-3">
                                <label for="priezvisko" class="form-label">Priezvisko</label>
                                <input type="text" name="priezvisko" class="form-control <?php echo (!empty($priezvisko_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $priezvisko; ?>" id="priezvisko" required>
                                <span class="invalid-feedback"><?php echo $priezvisko_err; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" id="email" required>
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>

                            <div class="mb-3">
                                <label for="heslo" class="form-label">Heslo</label>
                                <input type="password" name="heslo" class="form-control <?php echo (!empty($heslo_err)) ? 'is-invalid' : ''; ?>" id="heslo" required>
                                <span class="invalid-feedback"><?php echo $heslo_err; ?></span>
                                <div class="form-text text-light">Heslo musí obsahovať aspoň 6 znakov.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="heslo_potvrdenie" class="form-label">Potvrďte heslo</label>
                                <input type="password" name="heslo_potvrdenie" class="form-control <?php echo (!empty($heslo_potvrdenie_err)) ? 'is-invalid' : ''; ?>" id="heslo_potvrdenie" required>
                                <span class="invalid-feedback"><?php echo $heslo_potvrdenie_err; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn custom-btn w-100">Registrovať sa</button>
                            </div>
                            <p class="text-center">Už máte účet? <a href="prihlasenie.php" class="text-white">Prihláste sa</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT FILES -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/custom.js"></script>
</body>
</html>