<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

require_once "../db/config.php";
require_once "../db/model/Pouzivatel.php";

$email = $heslo = "";
$email_err = $heslo_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Kontrola, či bolo zadané používateľské meno
    if(empty(trim($_POST["email"]))){
        $email_err = "Zadajte emailovú adresu.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Kontrola, či bolo zadané heslo
    if(empty(trim($_POST["heslo"]))){
        $heslo_err = "Prosím, zadajte heslo.";
    } else{
        $heslo = trim($_POST["heslo"]);
    }
    
    // Validácia prihlasovacích údajov
    if(empty($email_err) && empty($heslo_err)){
        try {
            // Použitie statickej metódy verifyLogin z triedy Pouzivatel
            $pouzivatel = Pouzivatel::verifyLogin($email, $heslo);
            
            if ($pouzivatel) {
                //session_start();
                            
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $pouzivatel->getId();
                $_SESSION["meno"] = $pouzivatel->getMeno();
                $_SESSION["priezvisko"] = $pouzivatel->getPriezvisko();
                $_SESSION["email"] = $pouzivatel->getEmail();
                $_SESSION["je_admin"] = $pouzivatel->isAdmin();
                            
                if(isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("location: ../" . $redirect);
                } else {
                    header("location: ../index.php");
                }
                exit;
            } else {
                // Neúspešné prihlásenie
                $login_err = "Neplatný email alebo heslo.";
            }
        } catch (Exception $e) {
            // Zachytenie prípadných chýb
            $login_err = "Nastala chyba pri prihlasovaní: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prihlásenie - Gold Coffee</title>
    
    <!-- CSS FILES -->                
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
                        <h2 class="text-white">Prihlásenie</h2>
                        <a href="../index.php" class="text-white text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Späť na hlavnú stránku
                        </a>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(!empty($login_err)){
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }        
                        ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>    
                            <div class="mb-3">
                                <label for="heslo" class="form-label">Heslo</label>
                                <input type="password" name="heslo" class="form-control <?php echo (!empty($heslo_err)) ? 'is-invalid' : ''; ?>" required>
                                <span class="invalid-feedback"><?php echo $heslo_err; ?></span>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn custom-btn w-100">Prihlásiť sa</button>
                            </div>
                            <p class="text-center">Nemáte účet? <a href="registracia.php" class="text-white">Zaregistrujte sa</a></p>
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