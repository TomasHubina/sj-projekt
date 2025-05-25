<?php
// Inicializácia session
session_start();

// Kontrola či je používateľ prihlásený
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: autentification/prihlasenie.php");
    exit;
}

// Kontrola či je používateľ admin
if(!isset($_SESSION["je_admin"]) || $_SESSION["je_admin"] !== 1){
    header("location: index.php");
    exit;
}

// Pripojenie konfiguračného súboru
require_once "../db/config.php";
require_once "../functions/css.php";

// Spracovanie zmeny stavu objednávky
if(isset($_POST['update_stav']) && isset($_POST['objednavka_id']) && isset($_POST['novy_stav'])) {
    $objednavka_id = $_POST['objednavka_id'];
    $novy_stav = $_POST['novy_stav'];
    
    $update_sql = "UPDATE objednavky SET stav = ? WHERE objednavka_id = ?";
    if($stmt = mysqli_prepare($conn, $update_sql)) {
        mysqli_stmt_bind_param($stmt, "si", $novy_stav, $objednavka_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $success_message = "Stav objednávky č. $objednavka_id bol úspešne zmenený na: $novy_stav";
        } else {
            $error_message = "Chyba pri aktualizácii stavu: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Ak je nastavený parameter pre detail objednávky
$detail_objednavky = null;
$polozky_objednavky = array();

if(isset($_GET['detail']) && !empty($_GET['detail'])) {
    $objednavka_id = $_GET['detail'];
    
    // Získanie detailov objednávky
    $detail_sql = "SELECT o.*, p.meno FROM objednavky o 
                  LEFT JOIN pouzivatelia p ON o.pouzivatel_id = p.id 
                  WHERE o.objednavka_id = ?";
    
    if($stmt = mysqli_prepare($conn, $detail_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $objednavka_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $detail_objednavky = mysqli_fetch_assoc($result);
                
                // Získanie položiek objednávky
                $polozky_sql = "SELECT op.*, p.nazov, p.obrazok 
                              FROM objednavka_produkty op 
                              JOIN produkty p ON op.produkt_id = p.produkt_id 
                              WHERE op.objednavka_id = ?";
                
                if($stmt_polozky = mysqli_prepare($conn, $polozky_sql)) {
                    mysqli_stmt_bind_param($stmt_polozky, "i", $objednavka_id);
                    
                    if(mysqli_stmt_execute($stmt_polozky)) {
                        $result_polozky = mysqli_stmt_get_result($stmt_polozky);
                        
                        while($row = mysqli_fetch_assoc($result_polozky)) {
                            $polozky_objednavky[] = $row;
                        }
                    }
                    
                    mysqli_stmt_close($stmt_polozky);
                }
            } else {
                $error_message = "Objednávka nebola nájdená.";
            }
        } else {
            $error_message = "Chyba pri získavaní detailov objednávky: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Filtrovanie objednávok podľa stavu
$filter_stav = isset($_GET['stav']) ? $_GET['stav'] : '';
$where_clause = "";

if(!empty($filter_stav)) {
    $where_clause = " WHERE o.stav = '$filter_stav'";
}

// Získanie všetkých objednávok
$sql = "SELECT o.*, CONCAT(o.meno, ' ', o.priezvisko) AS zakaznik 
       FROM objednavky o$where_clause 
       ORDER BY o.datum_vytvorenia DESC";

$result = mysqli_query($conn, $sql);

// Získanie počtu objednávok podľa stavov
$stavy_sql = "SELECT stav, COUNT(*) as pocet FROM objednavky GROUP BY stav";
$stavy_result = mysqli_query($conn, $stavy_sql);
$stavy_pocty = array();

while($row = mysqli_fetch_assoc($stavy_result)) {
    $stavy_pocty[$row['stav']] = $row['pocet'];
}

$celkovy_pocet = array_sum($stavy_pocty);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa objednávok - Admin Panel</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link href="../css/tooplate-barista.css" rel="stylesheet">
    <?php admin_css(); ?>
    <style>
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        .order-detail-card {
            background-color: rgba(33, 37, 41, 0.85);
            margin-bottom: 1.5rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" target="_blank">Zobraziť stránku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/logout.php">Odhlásiť sa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section class="about-section section-padding" id="section_objednavky">
            <div class="container-fluid">
                <div class="row">
                    <!-- Bočný panel -->
                    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse admin-sidebar">
                        <div class="position-sticky pt-3">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" href="admin/index.php">
                                        <i class="bi bi-speedometer2"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="admin/produkty.php">
                                        <i class="bi bi-box"></i>
                                        Produkty
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="objednavky.php">
                                        <i class="bi bi-cart"></i>
                                        Objednávky
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="admin/pouzivatelia.php">
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
                                            Detail objednávky #<?php echo $detail_objednavky['objednavka_id']; ?>
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
                                                        <p><strong>Meno:</strong> <?php echo $detail_objednavky['meno']; ?></p>
                                                        <?php if(isset($detail_objednavky['email'])): ?>
                                                            <p><strong>Email:</strong> <?php echo $detail_objednavky['email']; ?></p>
                                                        <?php endif; ?>
                                                        <p><strong>Telefón:</strong> <?php echo $detail_objednavky['telefon']; ?></p>
                                                        <p><strong>Adresa:</strong> <?php echo $detail_objednavky['adresa']; ?></p>
                                                        <p><strong>Mesto:</strong> <?php echo $detail_objednavky['mesto']; ?>, <?php echo $detail_objednavky['psc']; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card order-detail-card">
                                                    <div class="card-header">
                                                        <i class="bi bi-truck me-1"></i> Informácie o objednávke
                                                    </div>
                                                    <div class="card-body">
                                                        <p><strong>ID objednávky:</strong> <?php echo $detail_objednavky['objednavka_id']; ?></p>
                                                        <p><strong>Dátum vytvorenia:</strong> <?php echo date('d.m.Y H:i', strtotime($detail_objednavky['datum_vytvorenia'])); ?></p>
                                                        <p><strong>Celková suma:</strong> <?php echo number_format($detail_objednavky['celkova_suma'], 2, ',', ' '); ?> €</p>
                                                        <p><strong>Spôsob platby:</strong> <?php echo $detail_objednavky['sposob_platby']; ?></p>
                                                        <p><strong>Spôsob doručenia:</strong> <?php echo $detail_objednavky['sposob_dorucenia']; ?></p>
                                                        <p>
                                                            <strong>Stav objednávky:</strong>
                                                            <?php 
                                                            $stav_trieda = "secondary";
                                                            if($detail_objednavky['stav'] == 'Nová') $stav_trieda = "info";
                                                            if($detail_objednavky['stav'] == 'Spracovaná') $stav_trieda = "primary";
                                                            if($detail_objednavky['stav'] == 'Odoslaná') $stav_trieda = "warning";
                                                            if($detail_objednavky['stav'] == 'Doručená') $stav_trieda = "success";
                                                            if($detail_objednavky['stav'] == 'Zrušená') $stav_trieda = "danger";
                                                            ?>
                                                            <span class="badge bg-<?php echo $stav_trieda; ?>"><?php echo $detail_objednavky['stav']; ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if(!empty($detail_objednavky['poznamka'])): ?>
                                        <div class="card order-detail-card mt-3">
                                            <div class="card-header">
                                                <i class="bi bi-chat-left-text me-1"></i> Poznámka k objednávke
                                            </div>
                                            <div class="card-body">
                                                <p><?php echo nl2br(htmlspecialchars($detail_objednavky['poznamka'])); ?></p>
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
                                                                <tr>
                                                                    <td>
                                                                        <?php if(!empty($polozka['obrazok'])): ?>
                                                                            <img src="images/products/<?php echo $polozka['obrazok']; ?>" alt="<?php echo $polozka['nazov']; ?>" width="40" class="me-2">
                                                                        <?php endif; ?>
                                                                        <?php echo $polozka['nazov']; ?>
                                                                    </td>
                                                                    <td><?php echo number_format($polozka['cena_za_kus'], 2, ',', ' '); ?> €</td>
                                                                    <td><?php echo $polozka['mnozstvo']; ?></td>
                                                                    <td><?php echo number_format($polozka['cena_za_kus'] * $polozka['mnozstvo'], 2, ',', ' '); ?> €</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Celková suma:</strong></td>
                                                                <td><strong><?php echo number_format($detail_objednavky['celkova_suma'], 2, ',', ' '); ?> €</strong></td>
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
                                                <form action="objednavky.php?detail=<?php echo $detail_objednavky['objednavka_id']; ?>" method="post" class="d-flex align-items-center">
                                                    <input type="hidden" name="objednavka_id" value="<?php echo $detail_objednavky['objednavka_id']; ?>">
                                                    <div class="me-3">
                                                        <select name="novy_stav" class="form-select bg-dark text-white">
                                                            <option value="Nová" <?php echo ($detail_objednavky['stav'] == 'Nová') ? 'selected' : ''; ?>>Nová</option>
                                                            <option value="Spracovaná" <?php echo ($detail_objednavky['stav'] == 'Spracovaná') ? 'selected' : ''; ?>>Spracovaná</option>
                                                            <option value="Odoslaná" <?php echo ($detail_objednavky['stav'] == 'Odoslaná') ? 'selected' : ''; ?>>Odoslaná</option>
                                                            <option value="Doručená" <?php echo ($detail_objednavky['stav'] == 'Doručená') ? 'selected' : ''; ?>>Doručená</option>
                                                            <option value="Zrušená" <?php echo ($detail_objednavky['stav'] == 'Zrušená') ? 'selected' : ''; ?>>Zrušená</option>
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
                                                    <?php if(mysqli_num_rows($result) > 0): ?>
                                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                                            <tr>
                                                                <td><?php echo $row['objednavka_id']; ?></td>
                                                                <td><?php echo htmlspecialchars($row['zakaznik']); ?></td>
                                                                <td><?php echo date('d.m.Y H:i', strtotime($row['datum_vytvorenia'])); ?></td>
                                                                <td><?php echo number_format($row['celkova_suma'], 2, ',', ' '); ?> €</td>
                                                                <td><?php echo $row['sposob_platby']; ?></td>
                                                                <td>
                                                                    <?php 
                                                                    $stav_trieda = "secondary";
                                                                    if($row['stav'] == 'Nová') $stav_trieda = "info";
                                                                    if($row['stav'] == 'Spracovaná') $stav_trieda = "primary";
                                                                    if($row['stav'] == 'Odoslaná') $stav_trieda = "warning";
                                                                    if($row['stav'] == 'Doručená') $stav_trieda = "success";
                                                                    if($row['stav'] == 'Zrušená') $stav_trieda = "danger";
                                                                    ?>
                                                                    <span class="badge bg-<?php echo $stav_trieda; ?> status-badge"><?php echo $row['stav']; ?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="objednavky.php?detail=<?php echo $row['objednavka_id']; ?>" class="btn btn-secondary btn-sm">
                                                                        <i class="bi bi-eye"></i> Detail
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
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

    <!-- JAVASCRIPT FILES -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/custom.js"></script>
</body>
</html>