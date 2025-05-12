<?php
// Inicializácia session
session_start();

// Kontrola či je používateľ prihlásený
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../autentification/prihlasenie.php");
    exit;
}

// Kontrola či je používateľ admin
if(!isset($_SESSION["je_admin"]) || $_SESSION["je_admin"] !== 1){
    header("location: ../index.php");
    exit;
}

// Pripojenie konfiguračného súboru
require_once "../db/config.php";

require_once "../functions/css.php";

// Získanie počtu objednávok
$sql = "SELECT COUNT(*) as pocet FROM objednavky";
$result = mysqli_query($conn, $sql);
$objednavky = mysqli_fetch_assoc($result)['pocet'];

// Získanie počtu produktov
$sql = "SELECT COUNT(*) as pocet FROM produkty";
$result = mysqli_query($conn, $sql);
$produkty = mysqli_fetch_assoc($result)['pocet'];

// Získanie celkového obratu
$sql = "SELECT SUM(celkova_suma) as obrat FROM objednavky";
$result = mysqli_query($conn, $sql);
$obrat = mysqli_fetch_assoc($result)['obrat'] ?: 0;

// Získanie počtu používateľov
$sql = "SELECT COUNT(*) as pocet FROM pouzivatelia";
$result = mysqli_query($conn, $sql);
$pouzivatelia = mysqli_fetch_assoc($result)['pocet'];
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link href="../css/tooplate-barista.css" rel="stylesheet">
    <?php admin_css(); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">Zobraziť stránku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Odhlásiť sa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="about-section section-padding" id="section_dashboard">

            <div class="container-fluid">
                <div class="row">
                    <!-- Bočný panel -->
                    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse admin-sidebar">
                        <div class="position-sticky pt-3">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link active" href="index.php">
                                        <i class="bi bi-speedometer2"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="produkty.php">
                                        <i class="bi bi-box"></i>
                                        Produkty
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="objednavky.php">
                                        <i class="bi bi-cart"></i>
                                        Objednávky
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="pouzivatelia.php">
                                        <i class="bi bi-people"></i>
                                        Používatelia
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <!-- Hlavný obsah -->
                    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="row" style="padding-top: 60px;">
                            <div class="col-12">
                                <em class="text-white">Administrátorský panel</em>
                                <h2 class="text-white mb-4">Dashboard</h2>
                                <div class="d-flex justify-content-end mb-4">
                                    <span class="badge bg-dark text-white">Dnes: <?php echo date("d.m.Y"); ?></span>
                                </div>
                            </div>

                            <!-- Štatistiky -->
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="display-6"><?php echo $objednavky; ?></div>
                                                <div>Objednávky</div>
                                            </div>
                                            <i class="bi bi-cart display-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="display-6"><?php echo $produkty; ?></div>
                                                <div>Produktov</div>
                                            </div>
                                            <i class="bi bi-box display-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="display-6"><?php echo number_format($obrat, 2, ',', ' '); ?> €</div>
                                                <div>Celkový obrat</div>
                                            </div>
                                            <i class="bi bi-currency-euro display-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="me-3">
                                                <div class="display-6"><?php echo $pouzivatelia; ?></div>
                                                <div>Používateľov</div>
                                            </div>
                                            <i class="bi bi-people display-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabuľky dát -->
                            <div class="col-lg-6">
                                <div class="card admin-card mb-4 text-white">
                                    <div class="card-header">
                                        <i class="bi bi-table me-1"></i>
                                        Posledné objednávky
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-dark table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Meno</th>
                                                        <th>Dátum</th>
                                                        <th>Suma</th>
                                                        <!--<th>Stav</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT o.*, p.meno FROM objednavky o
                                                            JOIN pouzivatelia p ON o.objednavka_id = p.id
                                                            ORDER BY o.datum_vytvorenia DESC LIMIT 5";
                                                    $result = mysqli_query($conn, $sql);
                                                    
                                                    if(mysqli_num_rows($result) > 0) {
                                                        while($row = mysqli_fetch_assoc($result)) {
                                                            echo "<tr>";
                                                            echo "<td>".$row['objednavka_id']."</td>";
                                                            echo "<td>".$row['meno']."</td>";
                                                            echo "<td>".date('d.m.Y', strtotime($row['datum_vytvorenia']))."</td>";
                                                            echo "<td>".number_format($row['celkova_suma'], 2, ',', ' ')." €</td>";
                                                            
                                                            /*$stav_trieda = "secondary";
                                                            if($row['stav'] == 'vybavená') $stav_trieda = "success";
                                                            else if($row['stav'] == 'zrušená') $stav_trieda = "danger";
                                                            else if($row['stav'] == 'spracováva sa') $stav_trieda = "warning";
                                                            
                                                            echo "<td><span class='badge bg-".$stav_trieda."'>".$row['stav']."</span></td>";
                                                            echo "</tr>";*/
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='5' class='text-center'>Žiadne objednávky</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="../objednavky.php" class="btn custom-btn custom-border-btn">Všetky objednávky</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="card admin-card mb-4 text-white">
                                    <div class="card-header">
                                        <i class="bi bi-star me-1"></i>
                                        Najpredávanejšie produkty
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-dark table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Produkt</th>
                                                        <th>Cena</th>
                                                        <th>Predané</th>
                                                        <th>Skladom</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT p.*, COUNT(op.id) as predane FROM produkty p
                                                            LEFT JOIN objednavka_produkty op ON p.produkt_id = op.produkt_id
                                                            GROUP BY p.produkt_id
                                                            ORDER BY predane DESC
                                                            LIMIT 5";
                                                    $result = mysqli_query($conn, $sql);
                                                    
                                                    if(mysqli_num_rows($result) > 0) {
                                                        while($row = mysqli_fetch_assoc($result)) {
                                                            echo "<tr>";
                                                            echo "<td>".$row['nazov']."</td>";
                                                            echo "<td>".number_format($row['cena'], 2, ',', ' ')." €</td>";
                                                            echo "<td>".$row['predane']."</td>";
                                                            
                                                            $sklad_trieda = "success";
                                                            if(isset($row['dostupne_mnozstvo'])) {
                                                                if($row['dostupne_mnozstvo'] < 5) $sklad_trieda = "warning";
                                                                if($row['dostupne_mnozstvo'] <= 0) $sklad_trieda = "danger";
                                                                echo "<td><span class='badge bg-".$sklad_trieda."'>".$row['dostupne_mnozstvo']."</span></td>";
                                                            } else {
                                                                echo "<td><span class='badge bg-secondary'>Nedostupné</span></td>";
                                                            }
                                                            
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='4' class='text-center'>Žiadne produkty</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="produkty.php" class="btn custom-btn custom-border-btn">Všetky produkty</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- JAVASCRIPT FILES -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/custom.js"></script>
</body>
</html>