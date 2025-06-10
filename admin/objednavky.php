<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true){
    $_SESSION['redirect_after_login'] = 'admin/objednavky.php';
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
require_once "../db/model/ObjednavkaPolozka.php";
require_once "../db/model/Produkt.php";
require_once "../db/model/Pouzivatel.php";

$success_message = null;
$error_message = null;

// Spracovanie zmeny stavu objednávky
if(isset($_POST['update_stav']) && isset($_POST['objednavka_id']) && isset($_POST['novy_stav'])) {
    $objednavka_id = $_POST['objednavka_id'];
    $novy_stav = $_POST['novy_stav'];
    
    try {
        $objednavka = Objednavka::findById($objednavka_id);
        if($objednavka) {
            $objednavka->setStav($novy_stav);
            if($objednavka->save()) {
                $success_message = "Stav objednávky č. $objednavka_id bol úspešne zmenený na: $novy_stav";
            } else {
                $error_message = "Chyba pri aktualizácii stavu objednávky";
            }
        } else {
            $error_message = "Objednávka nebola nájdená";
        }
    } catch (Exception $e) {
        $error_message = "Chyba pri aktualizácii stavu: " . $e->getMessage();
    }
}

$detail_objednavky = null;
$polozky_objednavky = array();

if(isset($_GET['detail']) && !empty($_GET['detail'])) {
    $objednavka_id = $_GET['detail'];
    
    try {
        $detail_objednavky = Objednavka::findById($objednavka_id);
        
        if($detail_objednavky) {
            $polozky_objednavky = $detail_objednavky->getPolozky();
        }
    } catch (Exception $e) {
        $error_message = "Chyba pri získavaní detailov objednávky: " . $e->getMessage();
    }
}

$filter_stav = isset($_GET['stav']) ? $_GET['stav'] : '';

try {
    if(!empty($filter_stav)) {
        $objednavky = Objednavka::findByStav($filter_stav);
    } else {
        $objednavky = Objednavka::getAll();
    }
    
    // Zoradenie objednávok podľa dátumu zostupne
    usort($objednavky, function($a, $b) {
        return strtotime($b->getDatumVytvorenia()) - strtotime($a->getDatumVytvorenia());
    });

    $stavy_pocty = Objednavka::countByStav();
    $celkovy_pocet = array_sum($stavy_pocty);
    
} catch (Exception $e) {
    $error_message = "Chyba pri načítaní objednávok: " . $e->getMessage();
    $objednavky = [];
    $stavy_pocty = [];
    $celkovy_pocet = 0;
}
?>

<!DOCTYPE html>
<html lang="sk">
<?php admin_head(); ?>
<body>
    
    <?php admin_navbar(); ?>

    <main>
        <section class="about-section section-padding" id="section_objednavky">
            <div class="container-fluid">
                <div class="row">
                    
                    <?php admin_sidebar(); ?>

                    <!-- Hlavný obsah -->
                    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="row" style="padding-top: 60px;">
                            <div class="col-12">
                                <em class="text-white">Administrácia</em>
                                <h2 class="text-white mb-4">Správa objednávok</h2>
                            </div>
                            
                            <?php if(isset($success_message)): ?>
                                <div class="col-12">
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo $success_message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(isset($error_message)): ?>
                                <div class="col-12">
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo $error_message; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Filtrovanie objednávok -->
                            <div class="col-12 mb-4">
                                <div class="card admin-card text-white">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <a href="objednavky.php" class="btn btn-sm <?php echo empty($filter_stav) ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Všetky (<?php echo $celkovy_pocet; ?>)
                                            </a>
                                            <a href="objednavky.php?stav=Nová" class="btn btn-sm <?php echo $filter_stav == 'Nová' ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Nové (<?php echo isset($stavy_pocty['Nová']) ? $stavy_pocty['Nová'] : 0; ?>)
                                            </a>
                                            <a href="objednavky.php?stav=Spracovaná" class="btn btn-sm <?php echo $filter_stav == 'Spracovaná' ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Spracované (<?php echo isset($stavy_pocty['Spracovaná']) ? $stavy_pocty['Spracovaná'] : 0; ?>)
                                            </a>
                                            <a href="objednavky.php?stav=Odoslaná" class="btn btn-sm <?php echo $filter_stav == 'Odoslaná' ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Odoslané (<?php echo isset($stavy_pocty['Odoslaná']) ? $stavy_pocty['Odoslaná'] : 0; ?>)
                                            </a>
                                            <a href="objednavky.php?stav=Doručená" class="btn btn-sm <?php echo $filter_stav == 'Doručená' ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Doručené (<?php echo isset($stavy_pocty['Doručená']) ? $stavy_pocty['Doručená'] : 0; ?>)
                                            </a>
                                            <a href="objednavky.php?stav=Zrušená" class="btn btn-sm <?php echo $filter_stav == 'Zrušená' ? 'custom-btn' : 'custom-btn custom-border-btn'; ?>">
                                                Zrušené (<?php echo isset($stavy_pocty['Zrušená']) ? $stavy_pocty['Zrušená'] : 0; ?>)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($detail_objednavky): ?>
                            <!-- Detail objednávky -->
                            <div class="col-12 mb-4">
                                <div class="card admin-card text-white">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Detail objednávky #<?php echo $detail_objednavky->getId(); ?>
                                        </div>
                                        <a href="objednavky.php" class="btn btn-sm custom-btn custom-border-btn">
                                            <i class="bi bi-arrow-left"></i> Späť na zoznam
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card order-detail-card">
                                                    <div class="card-header">
                                                        <i class="bi bi-person me-1"></i> Informácie o zákazníkovi
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>Meno:</strong> <?php echo $detail_objednavky->getMeno() . ' ' . $detail_objednavky->getPriezvisko(); ?></p>
                                                        <?php if($detail_objednavky->getEmail()): ?>
                                                            <p><strong>Email:</strong> <?php echo $detail_objednavky->getEmail(); ?></p>
                                                        <?php endif; ?>
                                                        <p><strong>Telefón:</strong> <?php echo $detail_objednavky->getTelefon(); ?></p>
                                                        <p><strong>Ulica:</strong> <?php echo $detail_objednavky->getUlica(); ?></p>
                                                        <p><strong>Cislo:</strong> <?php echo $detail_objednavky->getCislo(); ?></p>
                                                        <p><strong>Mesto:</strong> <?php echo $detail_objednavky->getMesto(); ?>, <?php echo $detail_objednavky->getPsc(); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card order-detail-card">
                                                    <div class="card-header">
                                                        <i class="bi bi-truck me-1"></i> Informácie o objednávke
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>ID objednávky:</strong> <?php echo $detail_objednavky->getId(); ?></p>
                                                        <p><strong>Dátum vytvorenia:</strong> <?php echo date('d.m.Y H:i', strtotime($detail_objednavky->getDatumVytvorenia())); ?></p>
                                                        <p><strong>Celková suma:</strong> <?php echo number_format($detail_objednavky->getCelkovaSuma(), 2, ',', ' '); ?> €</p>
                                                        <p><strong>Spôsob platby:</strong> <?php echo $detail_objednavky->getSposobPlatby(); ?></p>
                                                        <p><strong>Spôsob doručenia:</strong> <?php echo $detail_objednavky->getSposobDorucenia(); ?></p>
                                                        <p>
                                                            <strong>Stav objednávky:</strong>
                                                            <?php 
                                                            $stav_trieda = "secondary";
                                                            if($detail_objednavky->getStav() == 'Nová') $stav_trieda = "info";
                                                            if($detail_objednavky->getStav() == 'Spracovaná') $stav_trieda = "primary";
                                                            if($detail_objednavky->getStav() == 'Odoslaná') $stav_trieda = "warning";
                                                            if($detail_objednavky->getStav() == 'Doručená') $stav_trieda = "success";
                                                            if($detail_objednavky->getStav() == 'Zrušená') $stav_trieda = "danger";
                                                            ?>
                                                            <span class="badge bg-<?php echo $stav_trieda; ?>"><?php echo $detail_objednavky->getStav(); ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if($detail_objednavky->getPoznamka()): ?>
                                        <div class="card order-detail-card mt-3">
                                            <div class="card-header">
                                                <i class="bi bi-chat-left-text me-1"></i> Poznámka k objednávke
                                            </div>
                                            <div class="card-body">
                                                <p><?php echo nl2br(htmlspecialchars($detail_objednavky->getPoznamka())); ?></p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="card order-detail-card mt-3">
                                            <div class="card-header">
                                                <i class="bi bi-box me-1"></i> Položky objednávky
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-dark table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Produkt</th>
                                                                <th>Cena za kus</th>
                                                                <th>Množstvo</th>
                                                                <th>Spolu</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($polozky_objednavky as $polozka): ?>
                                                                <?php $produkt = $polozka->getProdukt(); ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php if($produkt && $produkt->getObrazok()): ?>
                                                                            <img src="../images/products/<?php echo $produkt->getObrazok(); ?>" alt="<?php echo $produkt->getNazov(); ?>" width="40" class="me-2">
                                                                        <?php endif; ?>
                                                                        <?php echo $produkt->getNazov(); ?>
                                                                    </td>
                                                                    <td><?php echo number_format($polozka->getCenaZaKus(), 2, ',', ' '); ?> €</td>
                                                                    <td><?php echo $polozka->getMnozstvo(); ?></td>
                                                                    <td><?php echo number_format($polozka->getCenaZaKus() * $polozka->getMnozstvo(), 2, ',', ' '); ?> €</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Celková suma:</strong></td>
                                                                <td><strong><?php echo number_format($detail_objednavky->getCelkovaSuma(), 2, ',', ' '); ?> €</strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card order-detail-card mt-3">
                                            <div class="card-header">
                                                <i class="bi bi-gear me-1"></i> Zmena stavu objednávky
                                            </div>
                                            <div class="card-body">
                                                <form action="objednavky.php?detail=<?php echo $detail_objednavky->getId(); ?>" method="post" class="d-flex align-items-center">
                                                    <input type="hidden" name="objednavka_id" value="<?php echo $detail_objednavky->getId(); ?>">
                                                    <div class="me-3">
                                                        <select name="novy_stav" class="form-select bg-dark text-white">
                                                            <option value="Nová" <?php echo ($detail_objednavky->getStav() == 'Nová') ? 'selected' : ''; ?>>Nová</option>
                                                            <option value="Spracovaná" <?php echo ($detail_objednavky->getStav() == 'Spracovaná') ? 'selected' : ''; ?>>Spracovaná</option>
                                                            <option value="Odoslaná" <?php echo ($detail_objednavky->getStav() == 'Odoslaná') ? 'selected' : ''; ?>>Odoslaná</option>
                                                            <option value="Doručená" <?php echo ($detail_objednavky->getStav() == 'Doručená') ? 'selected' : ''; ?>>Doručená</option>
                                                            <option value="Zrušená" <?php echo ($detail_objednavky->getStav() == 'Zrušená') ? 'selected' : ''; ?>>Zrušená</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="update_stav" class="btn custom-btn custom-border-btn">
                                                        <i class="bi bi-check-circle"></i> Aktualizovať stav
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- Zoznam objednávok -->
                            <div class="col-12">
                                <div class="card admin-card mb-4 text-white">
                                    <div class="card-header">
                                        <i class="bi bi-table me-1"></i>
                                        Zoznam objednávok
                                        <?php if(!empty($filter_stav)): ?>
                                            - Filtrované: <?php echo $filter_stav; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-dark table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>Zákazník</th>
                                                        <th>Dátum</th>
                                                        <th>Suma</th>
                                                        <th>Spôsob platby</th>
                                                        <th>Stav</th>
                                                        <th>Akcie</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(count($objednavky) > 0): ?>
                                                        <?php foreach($objednavky as $objednavka): ?>
                                                            <tr>
                                                                <td><?php echo $objednavka->getId(); ?></td>
                                                                <td><?php echo htmlspecialchars($objednavka->getMeno() . ' ' . $objednavka->getPriezvisko()); ?></td>
                                                                <td><?php echo date('d.m.Y H:i', strtotime($objednavka->getDatumVytvorenia())); ?></td>
                                                                <td><?php echo number_format($objednavka->getCelkovaSuma(), 2, ',', ' '); ?> €</td>
                                                                <td><?php echo $objednavka->getSposobPlatby(); ?></td>
                                                                <td>
                                                                    <?php 
                                                                    $stav_trieda = "secondary";
                                                                    if($objednavka->getStav() == 'Nová') $stav_trieda = "info";
                                                                    if($objednavka->getStav() == 'Spracovaná') $stav_trieda = "primary";
                                                                    if($objednavka->getStav() == 'Odoslaná') $stav_trieda = "warning";
                                                                    if($objednavka->getStav() == 'Doručená') $stav_trieda = "success";
                                                                    if($objednavka->getStav() == 'Zrušená') $stav_trieda = "danger";
                                                                    ?>
                                                                    <span class="badge bg-<?php echo $stav_trieda; ?> status-badge"><?php echo $objednavka->getStav(); ?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="objednavky.php?detail=<?php echo $objednavka->getId(); ?>" class="btn btn-secondary btn-sm">
                                                                        <i class="bi bi-eye"></i> Detail
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">Neboli nájdené žiadne objednávky.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php js(); ?>
</body>
</html>