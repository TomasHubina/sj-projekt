<?php
session_start();
require_once "db/config.php";

// Jednoduchý dopyt na získanie všetkých produktov
$sql = "SELECT * FROM produkty";
$result = mysqli_query($conn, $sql);

// Kontrola chyby pri dopyte
if (!$result) {
    die("Chyba pri načítaní produktov: " . mysqli_error($conn));
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

                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <div class="col-lg-4 col-md-6 col-12 mb-4">
                                <div class="menu-thumb">
                                    <div class="menu-image-wrap">
                                        <?php if(!empty($row['obrazok'])): ?>
                                            <img src="images/products/<?php echo htmlspecialchars($row['obrazok']); ?>" 
                                                 class="img-fluid menu-image" 
                                                 alt="<?php echo htmlspecialchars($row['nazov']); ?>">
                                        <?php else: ?>
                                            <div class="text-center py-5 bg-dark">
                                                <i class="bi bi-cup-hot text-white" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="menu-info d-flex flex-wrap align-items-center">
                                        <h4 class="text-white mb-0"><?php echo htmlspecialchars($row['nazov']); ?></h4>

                                        <span class="price-tag bg-dark shadow-lg ms-4 text-white">
                                            <small><?php echo number_format($row['cena'], 2, ',', ' '); ?> €</small>
                                        </span>

                                        <div class="d-flex flex-wrap align-items-center w-100 mt-2">
                                            <p class="text-white mb-0"><?php echo substr(htmlspecialchars($row['popis']), 0, 100); ?>...</p>

                                            <div class="mt-3 w-100">
                                                <?php 
                                                // Upravené pre použitie produkt_id namiesto id
                                                echo '<a href="produkt.php?id=' . $row['produkt_id'] . '" class="btn custom-btn">Zobraziť detail</a>';
                                                
                                                $dostupne = isset($row['dostupne_mnozstvo']) ? $row['dostupne_mnozstvo'] : 0;
                                                if($dostupne > 0) {
                                                    echo '<a href="kosik.php?action=add&id=' . $row['produkt_id'] . '&mnozstvo=1" class="btn custom-btn custom-border-btn ms-2">Do košíka</a>';
                                                } else {
                                                    echo '<button class="btn custom-btn bg-secondary ms-2" disabled>Nedostupné</button>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
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

    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>