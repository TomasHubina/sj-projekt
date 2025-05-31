<?php
session_start();
require_once "db/config.php";
require_once "db/model/Produkt.php";
require_once "functions/jsAcss.php";

try {
    $produkty = Produkt::getAll();
} catch (Exception $e) {
    die("Chyba pri načítaní produktov: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="sk">
<?php require_once "parts/head.php"; ?>
<body>
    <?php require_once "parts/nav.php"; ?>
    
    <main>
        <section class="about-section section-padding" id="section_produkty">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row" style="padding-top: 60px;">
                    <div class="col-12">
                        <em class="text-white">Naša ponuka</em>
                        <h2 class="text-white mb-4">Čerstvo upražená káva</h2>
                    </div>

                    <?php if(count($produkty) > 0): ?>
                        <?php foreach($produkty as $produkt): ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="menu-thumb">
                                    <div class="menu-image-wrap">
                                        <?php if(!empty($produkt->getObrazok())): ?>
                                            <img src="images/products/<?php echo htmlspecialchars($produkt->getObrazok()); ?>" 
                                                 class="img-fluid menu-image" 
                                                 alt="<?php echo htmlspecialchars($produkt->getNazov()); ?>">
                                        <?php else: ?>
                                            <div class="text-center py-5 bg-dark">
                                                <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="menu-info d-flex flex-wrap align-items-center">
                                        <h4 class="text-white mb-0"><?php echo htmlspecialchars($produkt->getNazov()); ?></h4>

                                        <span class="price-tag bg-dark shadow-lg ms-4 text-white">
                                            <small><?php echo number_format($produkt->getCena(), 2, ',', ' '); ?> €</small>
                                        </span>

                                        <div class="d-flex flex-wrap align-items-center w-100 mt-2">
                                            <p class="text-white mb-0"><?php echo substr(htmlspecialchars($produkt->getPopis()), 0, 100); ?>...</p>

                                            <div class="mt-3 w-100">
                                                <?php 
                                                echo '<a href="produkt.php?id=' . $produkt->getId() . '" class="btn custom-btn">Zobraziť detail</a>';
                                                
                                                $dostupne = $produkt->getDostupneMnozstvo();
                                                if($dostupne > 0) {
                                                    echo '<a href="kosik.php?action=add&id=' . $produkt->getId() . '&mnozstvo=1" class="btn custom-btn custom-border-btn ms-2">Do košíka</a>';
                                                } else {
                                                    echo '<button class="btn custom-btn bg-secondary ms-2" disabled>Nedostupné</button>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-dark text-white">
                                <p>Momentálne nemáme žiadne produkty na sklade.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php require_once "parts/footer.php"; ?>

    <?php js(); ?>
</body>
</html>