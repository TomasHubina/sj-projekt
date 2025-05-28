<?php
session_start();

require_once "db/config.php";

$ma_objednavku = isset($_SESSION['objednavka_id']) && !empty($_SESSION['objednavka_id']);
$objednavka_id = $ma_objednavku ? $_SESSION['objednavka_id'] : 0;

$objednavka = null;
$polozky = [];

if ($ma_objednavku) {
    $query = "SELECT * FROM objednavky WHERE objednavka_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $objednavka_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $objednavka = $row;
        
        $query_polozky = "SELECT op.*, p.nazov, p.obrazok 
                         FROM objednavka_produkty op
                         JOIN produkty p ON op.produkt_id = p.produkt_id
                         WHERE op.objednavka_id = ?";
        $stmt_polozky = mysqli_prepare($conn, $query_polozky);
        mysqli_stmt_bind_param($stmt_polozky, "i", $objednavka_id);
        mysqli_stmt_execute($stmt_polozky);
        $result_polozky = mysqli_stmt_get_result($stmt_polozky);
        
        while ($polozka = mysqli_fetch_assoc($result_polozky)) {
            $polozky[] = $polozka;
        }
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
                                            <?php echo htmlspecialchars($objednavka['meno'] . ' ' . $objednavka['priezvisko']); ?><br>
                                            Email: <?php echo htmlspecialchars($objednavka['email']); ?><br>
                                            Telefón: <?php echo htmlspecialchars($objednavka['telefon']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="text-white">Adresa doručenia</h5>
                                        <p class="text-white">
                                            <?php echo htmlspecialchars($objednavka['ulica'] . ' ' . $objednavka['cislo']); ?><br>
                                            <?php echo htmlspecialchars($objednavka['mesto']); ?><br>
                                            <?php echo htmlspecialchars($objednavka['psc']); ?>
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
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if(!empty($polozka['obrazok'])): ?>
                                                            <img src="images/products/<?php echo htmlspecialchars($polozka['obrazok']); ?>" 
                                                                class="img-fluid rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center me-2" 
                                                                style="width: 50px; height: 50px;">
                                                                <i class="bi bi-cup-hot text-white"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php echo htmlspecialchars($polozka['nazov']); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo $polozka['mnozstvo']; ?></td>
                                                <td><?php echo number_format($polozka['cena_za_kus'], 2, ',', ' '); ?> €</td>
                                                <td><?php echo number_format($polozka['celkova_suma'], 2, ',', ' '); ?> €</td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Celková suma:</strong></td>
                                                <td><strong><?php echo number_format($objednavka['celkova_suma'], 2, ',', ' '); ?> €</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <?php if (!empty($objednavka['poznamka'])): ?>
                                <div class="mt-4">
                                    <h5 class="text-white">Poznámka k objednávke</h5>
                                    <p class="text-white"><?php echo nl2br(htmlspecialchars($objednavka['poznamka'])); ?></p>
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
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>