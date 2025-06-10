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
 
    if(empty(trim($_POST["email"]))){
        $email_err = "Zadajte emailovú adresu.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    if(empty(trim($_POST["heslo"]))){
        $heslo_err = "Prosím, zadajte heslo.";
    } else{
        $heslo = trim($_POST["heslo"]);
    }
    
    if(empty($email_err) && empty($heslo_err)){
        try {
            $pouzivatel = Pouzivatel::verifyLogin($email, $heslo);
            
            if ($pouzivatel) {                            
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
                $login_err = "Neplatný email alebo heslo.";
            }
        } catch (Exception $e) {
            $login_err = "Nastala chyba pri prihlasovaní: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="sk">
<?php require_once "../parts/head.php"; ?>
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