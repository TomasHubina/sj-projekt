<?php
session_start();
require_once "db/config.php";

// Overenie, či bol zadaný ID produktu
if(!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: produkty.php");
    exit;
}

// Získanie detailov produktu
$id = $_GET["id"];
// Upravený SQL dotaz pre použitie produkt_id namiesto id
$sql = "SELECT * FROM produkty WHERE produkt_id = ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1) {
            $produkt = mysqli_fetch_assoc($result);
        } else {
            // Produkt nebol nájdený
            header("location: produkty.php");
            exit;
        }
    } else {
        echo "Ups! Niečo sa pokazilo. Skúste to neskôr.";
        exit;
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Ups! Niečo sa pokazilo. Skúste to neskôr.";
    exit;
}

// Získanie súvisiacich produktov
$suvisiace_produkty = array();

// Skontrolujeme, či existuje kategória
if(isset($produkt["kategoria"]) && !empty($produkt["kategoria"])) {
    // Upravený SQL dotaz pre použitie produkt_id namiesto id
    $sql_suvisiace = "SELECT * FROM produkty WHERE kategoria = ? AND produkt_id != ? LIMIT 3";
    
    if($stmt = mysqli_prepare($conn, $sql_suvisiace)) {
        mysqli_stmt_bind_param($stmt, "si", $produkt["kategoria"], $id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            while($row = mysqli_fetch_assoc($result)) {
                $suvisiace_produkty[] = $row;
            }
        }
        
        mysqli_stmt_close($stmt);
    }
} else {
    // Ak neexistuje kategória, vyberieme náhodné produkty
    // Upravený SQL dotaz pre použitie produkt_id namiesto id
    $sql_suvisiace = "SELECT * FROM produkty WHERE produkt_id != ? ORDER BY RAND() LIMIT 3";
    
    if($stmt = mysqli_prepare($conn, $sql_suvisiace)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            while($row = mysqli_fetch_assoc($result)) {
                $suvisiace_produkty[] = $row;
            }
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Zatvorenie spojenia
mysqli_close($conn);

// Zjednotenie názvov stĺpcov
$dostupne_mnozstvo = isset($produkt["dostupne_mnozstvo"]) ? $produkt["dostupne_mnozstvo"] : 
                    (isset($produkt["mnozstvo_na_sklade"]) ? $produkt["mnozstvo_na_sklade"] : 0);
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
                                <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($produkt["nazov"]); ?></li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="col-lg-6 col-12">
                        <div class="bg-dark p-4 rounded">
                            <?php if(!empty($produkt["obrazok"])): ?>
                                <img src="images/products/<?php echo htmlspecialchars($produkt["obrazok"]); ?>" 
                                     class="img-fluid product-image" 
                                     alt="<?php echo htmlspecialchars($produkt["nazov"]); ?>">
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-cup-hot text-white" style="font-size: 8rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-12 mt-4 mt-lg-0">
                        <div class="bg-dark p-4 rounded">
                            <h2 class="text-white mb-2"><?php echo htmlspecialchars($produkt["nazov"]); ?></h2>
                            
                            <?php if(isset($produkt["kategoria"]) && !empty($produkt["kategoria"])): ?>
                                <p class="text-light mb-4">Kategória: <?php echo htmlspecialchars($produkt["kategoria"]); ?></p>
                            <?php endif; ?>
                            
                            <div class="price-tag bg-dark shadow-lg d-inline-block mb-4">
                                <h3 class="text-white mb-0"><?php echo number_format($produkt["cena"], 2, ',', ' '); ?> €</h3>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-white"><?php echo nl2br(htmlspecialchars($produkt["popis"])); ?></p>
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
                                    <!-- Upravený odkaz pre použitie produkt_id namiesto id -->
                                    <input type="hidden" name="id" value="<?php echo $produkt["produkt_id"]; ?>">
                                    
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
                                <?php if(!empty($sp['obrazok'])): ?>
                                    <img src="images/products/<?php echo htmlspecialchars($sp['obrazok']); ?>" 
                                         class="img-fluid menu-image" 
                                         alt="<?php echo htmlspecialchars($sp['nazov']); ?>">
                                <?php else: ?>
                                    <div class="text-center py-5 bg-dark">
                                        <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="menu-info d-flex flex-wrap align-items-center">
                                <h4 class="text-white mb-0"><?php echo htmlspecialchars($sp['nazov']); ?></h4>

                                <span class="price-tag bg-dark shadow-lg ms-4 text-white">
                                    <small><?php echo number_format($sp['cena'], 2, ',', ' '); ?> €</small>
                                </span>

                                <div class="mt-2 w-100">
                                    <!-- Upravený odkaz pre použitie produkt_id namiesto id -->
                                    <a href="produkt.php?id=<?php echo $sp['produkt_id']; ?>" class="btn custom-btn mt-2">
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

    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>