<?php
session_start();
require_once "db/config.php";

if(!isset($_SESSION['kosik'])) {
    $_SESSION['kosik'] = array();
}

if(isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $produkt_id = $_GET['id'];
    $mnozstvo = isset($_GET['mnozstvo']) ? (int)$_GET['mnozstvo'] : 1;
    
    $sql = "SELECT * FROM produkty WHERE produkt_id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $produkt_id);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($produkt = mysqli_fetch_assoc($result)) {
                $dostupne_mnozstvo = isset($produkt['dostupne_mnozstvo']) ? $produkt['dostupne_mnozstvo'] : 0;
                
                if($dostupne_mnozstvo >= $mnozstvo) {
                    if(isset($_SESSION['kosik'][$produkt_id])) {
                        $_SESSION['kosik'][$produkt_id]['mnozstvo'] += $mnozstvo;
                    } else {
                        $_SESSION['kosik'][$produkt_id] = array(
                            'id' => $produkt['produkt_id'], 
                            'nazov' => $produkt['nazov'],
                            'cena' => $produkt['cena'],
                            'mnozstvo' => $mnozstvo,
                            'obrazok' => $produkt['obrazok']
                        );
                    }
                    header("Location: kosik.php?status=added");
                    exit;
                } else {
                    header("Location: produkt.php?id=".$produkt_id."&error=stock");
                    exit;
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Odstránenie produktu z košíka
if(isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $produkt_id = $_GET['id'];
    if(isset($_SESSION['kosik'][$produkt_id])) {
        unset($_SESSION['kosik'][$produkt_id]);
    }
    header("Location: kosik.php");
    exit;
}

// Aktualizácia množstva
if(isset($_POST['update_kosik'])) {
    foreach($_POST['mnozstvo'] as $id => $mnozstvo) {
        if((int)$mnozstvo > 0) {
            $_SESSION['kosik'][$id]['mnozstvo'] = (int)$mnozstvo;
        }
    }
    header("Location: kosik.php");
    exit;
}

// Vyprázdnenie košíka
if(isset($_GET['action']) && $_GET['action'] == 'empty') {
    $_SESSION['kosik'] = array();
    header("Location: kosik.php");
    exit;
}

// Výpočet celkovej sumy
$celkova_suma = 0;
foreach($_SESSION['kosik'] as $item) {
    $celkova_suma += $item['cena'] * $item['mnozstvo'];
}
?>

<!DOCTYPE html>
<html lang="sk">
<?php require_once "parts/head.php"; ?>
<body>
    <?php require_once "parts/nav.php"; ?>
    
    <main>
        <section class="about-section section-padding" id="section_kosik">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row" style="padding-top: 60px;">
                    <div class="col-12">
                        <em class="text-white">Váš nákup</em>
                        <h2 class="text-white mb-4">Nákupný košík</h2>
                    </div>
                    
                    <?php if(count($_SESSION['kosik']) > 0): ?>
                        <div class="col-12 bg-dark p-4 rounded shadow-sm">
                            <form action="kosik.php" method="post">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Produkt</th>
                                                <th>Cena</th>
                                                <th>Množstvo</th>
                                                <th>Medzisúčet</th>
                                                <th>Akcia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($_SESSION['kosik'] as $id => $item): ?>
                                            <tr>
                                                <td>
                                                    <?php if(!empty($item['obrazok'])): ?>
                                                        <img src="images/products/<?php echo htmlspecialchars($item['obrazok']); ?>" alt="<?php echo htmlspecialchars($item['nazov']); ?>" width="50" class="me-2">
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($item['nazov']); ?>
                                                </td>
                                                <td><?php echo number_format($item['cena'], 2, ',', ' '); ?> €</td>
                                                <td>
                                                    <input type="number" name="mnozstvo[<?php echo $id; ?>]" value="<?php echo $item['mnozstvo']; ?>" min="1" class="form-control form-control-sm bg-dark text-white" style="width: 80px;">
                                                </td>
                                                <td><?php echo number_format($item['cena'] * $item['mnozstvo'], 2, ',', ' '); ?> €</td>
                                                <td>
                                                    <a href="kosik.php?action=remove&id=<?php echo $id; ?>" class="btn btn-sm custom-btn bg-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold">Celková suma:</td>
                                                <td><?php echo number_format($celkova_suma, 2, ',', ' '); ?> €</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="produkty.php" class="btn custom-btn custom-border-btn">
                                        <i class="bi bi-arrow-left"></i> Pokračovať v nákupe
                                    </a>
                                    <div>
                                        <button type="submit" name="update_kosik" class="btn custom-btn custom-border-btn me-2">
                                            <i class="bi bi-arrow-clockwise"></i> Uložiť košík
                                        </button>
                                        <a href="objednavka.php" class="btn custom-btn">
                                            <i class="bi bi-check-circle"></i> Dokončiť objednávku
                                        </a>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="text-center mt-4">
                                <a href="kosik.php?action=empty" class="text-danger">
                                    <i class="bi bi-trash"></i> Vyprázdniť košík
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-dark text-white">
                                <p>Váš košík je prázdny.</p>
                            </div>
                            <a href="produkty.php" class="btn custom-btn">
                                <i class="bi bi-cart-plus"></i> Prejsť na nákup
                            </a>
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