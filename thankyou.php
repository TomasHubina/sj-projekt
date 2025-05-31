<?php
session_start();

require_once "db/config.php";
require_once "db/model/Objednavka.php";
require_once "db/model/ObjednavkaPolozka.php";
require_once "db/model/Produkt.php";
require_once "functions/jsAcss.php";

$ma_objednavku = isset($_SESSION['objednavka_id']) && !empty($_SESSION['objednavka_id']);
$objednavka_id = $ma_objednavku ? $_SESSION['objednavka_id'] : 0;

$objednavka = null;
$polozky = [];

if ($ma_objednavku) {
    try {
        $objednavka = Objednavka::findById($objednavka_id);
        
        if ($objednavka) {
            $polozky = ObjednavkaPolozka::findByObjednavkaId($objednavka_id);
        }
    } catch (Exception $e) {
        echo "Chyba pri načítaní objednávky: " . $e->getMessage();
    }
}

if ($ma_objednavku) {
    unset($_SESSION['kosik']);
}

$file_path = "parts/head.php";
if(!require($file_path)) {
    die("Chyba pri načítaní head.php");
}
?>

<body>
    <?php include "parts/nav.php"; ?>

    <main>
        <section class="about-section section-padding" style="padding-top: 160px;">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
                        <h1 class="text-white mt-4">Ďakujeme za Vašu objednávku!</h1>
                        <?php if ($ma_objednavku): ?>
                            <p class="text-white">Vaša objednávka s číslom <strong>#<?php echo $objednavka_id; ?></strong> bola úspešne prijatá.</p>
                        <?php else: ?>
                            <p class="text-white">Vaša objednávka bola úspešne prijatá.</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($ma_objednavku && $objednavka): ?>
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="card bg-dark text-white mb-4">
                            <div class="card-header">
                                <h3 class="text-white">Detaily objednávky</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5 class="text-white">Kontaktné údaje</h5>
                                        <p class="text-white">
                                            <?php echo htmlspecialchars($objednavka->getMeno() . ' ' . $objednavka->getPriezvisko()); ?><br>
                                            Email: <?php echo htmlspecialchars($objednavka->getEmail()); ?><br>
                                            Telefón: <?php echo htmlspecialchars($objednavka->getTelefon()); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="text-white">Adresa doručenia</h5>
                                        <p class="text-white">
                                            <?php echo htmlspecialchars($objednavka->getUlica() . ' ' . $objednavka->getCislo()); ?><br>
                                            <?php echo htmlspecialchars($objednavka->getMesto()); ?><br>
                                            <?php echo htmlspecialchars($objednavka->getPsc()); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <h5 class="text-white">Objednané položky</h5>
                                <div class="table-responsive">
                                    <table class="table table-dark">
                                        <thead>
                                            <tr>
                                                <th>Produkt</th>
                                                <th>Množstvo</th>
                                                <th>Cena za kus</th>
                                                <th>Cena spolu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($polozky as $polozka): ?>
                                            <?php $produkt = $polozka->getProdukt(); ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if(!empty($produkt->getObrazok())): ?>
                                                            <img src="images/products/<?php echo htmlspecialchars($produkt->getObrazok()); ?>" 
                                                                class="img-fluid rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-2" 
                                                                style="width: 50px; height: 50px;">
                                                                <i class="bi bi-cup-hot text-white"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php echo htmlspecialchars($produkt->getNazov()); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo $polozka->getMnozstvo(); ?></td>
                                                <td><?php echo number_format($polozka->getCenaZaKus(), 2, ',', ' '); ?> €</td>
                                                <td><?php echo number_format($polozka->getCelkovaSuma(), 2, ',', ' '); ?> €</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Celková suma:</strong></td>
                                                <td><strong><?php echo number_format($objednavka->getCelkovaSuma(), 2, ',', ' '); ?> €</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <?php if (!empty($objednavka->getPoznamka())): ?>
                                <div class="mt-4">
                                    <h5 class="text-white">Poznámka k objednávke</h5>
                                    <p class="text-white"><?php echo nl2br(htmlspecialchars($objednavka->getPoznamka())); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-12 text-center mt-4">
                        <p class="text-white">Na Vašu emailovú adresu sme odoslali potvrdenie objednávky.</p>
                        <p class="text-white mb-4">O ďalších krokoch Vás budeme informovať.</p>
                        <a href="index.php" class="btn custom-btn">Späť na hlavnú stránku</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include "parts/footer.php"; ?>
    <?php js(); ?>
</body>
</html>