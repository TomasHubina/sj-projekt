<?php
session_start();
require_once "db/config.php";
require_once "db/model/Produkt.php";
require_once "functions/jsAcss.php";


if(!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: produkty.php");
    exit;
}

$id = $_GET["id"];

try {
    $produkt = Produkt::findById($id);
    
    if(!$produkt) {
        header("location: produkty.php");
        exit;
    }
    
    $suvisiace_produkty = Produkt::getRandomProducts($id, 3);
    
} catch (Exception $e) {
    echo "Niečo sa pokazilo: " . $e->getMessage();
    exit;
}

$dostupne_mnozstvo = $produkt->getDostupneMnozstvo();
?>

<!DOCTYPE html>
<html lang="sk">
<?php require_once "parts/head.php"; ?>
<body>
    <?php require_once "parts/nav.php"; ?>
    
    <main>
        <section class="about-section section-padding" id="section_detail">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row" style="padding-top: 60px;">
                    <div class="col-12 mb-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php" class="text-white">Domov</a></li>
                                <li class="breadcrumb-item"><a href="produkty.php" class="text-white">Produkty</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($produkt->getNazov()); ?></li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="col-lg-6 col-12">
                        <div class="bg-dark p-4 rounded">
                            <?php if(!empty($produkt->getObrazok())): ?>
                                <img src="images/products/<?php echo htmlspecialchars($produkt->getObrazok()); ?>" 
                                     class="img-fluid product-image" 
                                     alt="<?php echo htmlspecialchars($produkt->getNazov()); ?>">
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-cup-hot text-white" style="font-size: 8rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-12 mt-4 mt-lg-0">
                        <div class="bg-dark p-4 rounded">
                            <h2 class="text-white mb-2"><?php echo htmlspecialchars($produkt->getNazov()); ?></h2>
                            
                            <div class="price-tag bg-dark shadow-lg d-inline-block mb-4">
                                <h3 class="text-white mb-0"><?php echo number_format($produkt->getCena(), 2, ',', ' '); ?> €</h3>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-white"><?php echo nl2br(htmlspecialchars($produkt->getPopis())); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-white mb-2">
                                    <strong>Dostupnosť:</strong> 
                                    <?php if($dostupne_mnozstvo > 10): ?>
                                        <span class="badge bg-success">Na sklade</span>
                                    <?php elseif($dostupne_mnozstvo > 0): ?>
                                        <span class="badge bg-warning text-dark">Posledné kusy</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Vypredané</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <?php if($dostupne_mnozstvo > 0): ?>
                                <form action="kosik.php" method="get" class="mb-4">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?php echo $produkt->getId(); ?>">
                                    
                                    <div class="row g-3 align-items-center mb-4">
                                        <div class="col-auto">
                                            <label for="mnozstvo" class="col-form-label text-white">Množstvo:</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="number" id="mnozstvo" name="mnozstvo" class="form-control bg-dark text-white" 
                                                   value="1" min="1" max="<?php echo $dostupne_mnozstvo; ?>">
                                        </div>
                                        <div class="col-auto">
                                            <span class="form-text text-white">(Dostupné: <?php echo $dostupne_mnozstvo; ?>)</span>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn custom-btn custom-border-btn">
                                        <i class="bi bi-cart-plus me-2"></i> Pridať do košíka
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-dark">
                                    <i class="bi bi-exclamation-triangle me-2"></i> Tento produkt momentálne nie je dostupný.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Súvisiace produkty -->
        <?php if(count($suvisiace_produkty) > 0): ?>
        <section class="about-section section-padding" id="section_suvisiace">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3 class="text-white mb-4">Mohlo by vás zaujímať</h3>
                    </div>
                    
                    <?php foreach($suvisiace_produkty as $sp): ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-4">
                        <div class="menu-thumb">
                            <div class="menu-image-wrap">
                                <?php if(!empty($sp->getObrazok())): ?>
                                    <img src="images/products/<?php echo htmlspecialchars($sp->getObrazok()); ?>" 
                                         class="img-fluid menu-image" 
                                         alt="<?php echo htmlspecialchars($sp->getNazov()); ?>">
                                <?php else: ?>
                                    <div class="text-center py-5 bg-dark">
                                        <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="menu-info d-flex flex-wrap align-items-center">
                                <h4 class="text-white mb-0"><?php echo htmlspecialchars($sp->getNazov()); ?></h4>

                                <span class="price-tag bg-dark shadow-lg ms-4 text-white">
                                    <small><?php echo number_format($sp->getCena(), 2, ',', ' '); ?> €</small>
                                </span>

                                <div class="mt-2 w-100">
                                    <a href="produkt.php?id=<?php echo $sp->getId(); ?>" class="btn custom-btn mt-2">
                                        Zobraziť detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>
    
    <?php require_once "parts/footer.php"; ?>

    <?php js(); ?>
</body>
</html>