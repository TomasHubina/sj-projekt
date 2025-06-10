<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    $_SESSION['redirect_after_login'] = 'admin/index.php';
    header("location: ../autentification/prihlasenie.php");
    exit;
}

if(!isset($_SESSION["je_admin"]) || $_SESSION["je_admin"] != 1){
    header("location: ../index.php");
    exit;
}

require_once "../db/config.php";
require_once "../functions/admin_parts.php";
require_once "../functions/js.php";
require_once "../db/model/Objednavka.php";
require_once "../db/model/Produkt.php";
require_once "../db/model/Pouzivatel.php";
require_once "../db/model/ObjednavkaPolozka.php";

$objednavky_list = Objednavka::getAll();
$objednavky = count($objednavky_list);

$produkty_list = Produkt::getAll();
$produkty = count($produkty_list);

$obrat = 0;
foreach ($objednavky_list as $objednavka) {
    $obrat += $objednavka->getCelkovaSuma();
}

$pouzivatelia_list = Pouzivatel::getAll();
$pouzivatelia = count($pouzivatelia_list);
?>

<!DOCTYPE html>
<html lang="sk">
<?php admin_head(); ?>
<body>
    
    <?php admin_navbar(); ?>

    <main>
        <section class="about-section section-padding" id="section_dashboard">

            <div class="container-fluid">
                <div class="row">
                    
                    <?php admin_sidebar(); ?>

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
                                                        <th>Stav</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    usort($objednavky_list, function($a, $b) {
                                                        return strtotime($b->getDatumVytvorenia()) - strtotime($a->getDatumVytvorenia());
                                                    });
                                                    
                                                    $recent_objednavky = array_slice($objednavky_list, 0, 5);
                                                    
                                                    if(count($recent_objednavky) > 0) {
                                                        foreach($recent_objednavky as $objednavka) {
                                                            $pouzivatel = $objednavka->getPouzivatel();

                                                            echo "<tr>";
                                                            echo "<td>".$objednavka->getId()."</td>";
                                                            echo "<td>".$objednavka->getMeno()."</td>";
                                                            echo "<td>".date('d.m.Y', strtotime($objednavka->getDatumVytvorenia()))."</td>";
                                                            echo "<td>".number_format($objednavka->getCelkovaSuma(), 2, ',', ' ')." €</td>";
                                                            
                                                            $stav_trieda = "secondary";
                                                            if($objednavka->getStav() == 'Nová') $stav_trieda = "info";
                                                            if($objednavka->getStav() == 'Spracovaná') $stav_trieda = "primary";
                                                            if($objednavka->getStav() == 'Odoslaná') $stav_trieda = "warning";
                                                            if($objednavka->getStav() == 'Doručená') $stav_trieda = "success";
                                                            if($objednavka->getStav() == 'Zrušená') $stav_trieda = "danger";
                                                            
                                                            echo "<td><span class='badge bg-".$stav_trieda."'>".$objednavka->getStav()."</span></td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='5' class='text-center'>Žiadne objednávky</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="objednavky.php" class="btn custom-btn custom-border-btn">Všetky objednávky</a>
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
                                                    $predane_produkty = [];
                                                    
                                                    foreach($objednavky_list as $objednavka) {
                                                        $polozky = $objednavka->getPolozky();
                                                        foreach($polozky as $polozka) {
                                                            $produkt_id = $polozka->getProduktId();
                                                            $mnozstvo = $polozka->getMnozstvo();
                                                            
                                                            if(!isset($predane_produkty[$produkt_id])) {
                                                                $predane_produkty[$produkt_id] = [
                                                                    'produkt' => Produkt::findById($produkt_id),
                                                                    'predane' => 0
                                                                ];
                                                            }
                                                            
                                                            $predane_produkty[$produkt_id]['predane'] += $mnozstvo;
                                                        }
                                                    }
                                                    
                                                    uasort($predane_produkty, function($a, $b) {
                                                        return $b['predane'] - $a['predane'];
                                                    });
                                                    
                                                    $najpredavanejsie = array_slice($predane_produkty, 0, 5);
                                                    
                                                    if(count($najpredavanejsie) > 0) {
                                                        foreach($najpredavanejsie as $data) {
                                                            $produkt = $data['produkt'];
                                                            $predane = $data['predane'];

                                                            echo "<tr>";
                                                            echo "<td>".$produkt->getNazov()."</td>";
                                                            echo "<td>".number_format($produkt->getCena(), 2, ',', ' ')." €</td>";
                                                            echo "<td>".$predane."</td>";
                                                            
                                                            $sklad_trieda = "success";
                                                            $mnozstvo = $produkt->getDostupneMnozstvo();

                                                                if($mnozstvo < 5) $sklad_trieda = "warning";
                                                                if($mnozstvo <= 0) $sklad_trieda = "danger";

                                                                echo "<td><span class='badge bg-".$sklad_trieda."'>".$mnozstvo."</span></td>";
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

    <?php js(); ?>
</body>
</html>