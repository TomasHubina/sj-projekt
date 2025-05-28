<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["je_admin"]) || $_SESSION["je_admin"] !== 1) {
    header("location: ../authentification/prihlasenie.php");
    exit;
}

require_once "../db/config.php";
require_once "../functions/admin_css.php";
require_once "../functions/admin_parts.php";

$nazov = $popis = $cena = $mnozstvo = $obrazok = "";
$nazov_err = $popis_err = $cena_err = $mnozstvo_err = $obrazok_err = "";
$edit_id = 0;

// Mazanie produktu
if(isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $sql = "DELETE FROM produkty WHERE produkt_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET["delete"]);
        
        if(mysqli_stmt_execute($stmt)) {
            header("location: produkty.php?status=deleted");
            exit;
        } else {
            echo "Ups! Niečo sa pokazilo. Skúste to neskôr.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Spracovanie formulára pre pridanie/úpravu
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["nazov"]))) {
        $nazov_err = "Zadajte názov produktu.";
    } else {
        $nazov = trim($_POST["nazov"]);
    }
    
    if(empty(trim($_POST["popis"]))) {
        $popis_err = "Zadajte popis produktu.";
    } else {
        $popis = trim($_POST["popis"]);
    }
    
    if(empty(trim($_POST["cena"]))) {
        $cena_err = "Zadajte cenu produktu.";
    } elseif(!is_numeric($_POST["cena"]) || $_POST["cena"] <= 0) {
        $cena_err = "Zadajte platnú cenu.";
    } else {
        $cena = trim($_POST["cena"]);
    }
    
    if(empty(trim($_POST["mnozstvo"]))) {
        $mnozstvo_err = "Zadajte množstvo na sklade.";
    } elseif(!is_numeric($_POST["mnozstvo"]) || $_POST["mnozstvo"] < 0) {
        $mnozstvo_err = "Zadajte platné množstvo.";
    } else {
        $mnozstvo = trim($_POST["mnozstvo"]);
    }
    
    $obrazok_nazov = "";
    if(isset($_FILES["obrazok"]) && $_FILES["obrazok"]["error"] == 0) {
        $povolene_typy = array("jpg", "jpeg", "png", "gif");
        $tmp_nazov = $_FILES["obrazok"]["tmp_name"];
        $obrazok_nazov = basename($_FILES["obrazok"]["name"]);
        $obrazok_velkost = $_FILES["obrazok"]["size"];
        $obrazok_ext = strtolower(pathinfo($obrazok_nazov, PATHINFO_EXTENSION));
        
        if(!in_array($obrazok_ext, $povolene_typy)) {
            $obrazok_err = "Povolené sú len súbory JPG, JPEG, PNG a GIF.";
        }
        
        if($obrazok_velkost > 5242880) {
            $obrazok_err = "Súbor je príliš veľký. Maximálna veľkosť je 5MB.";
        }
        
        if(empty($obrazok_err)) {
            $new_filename = uniqid() . '.' . $obrazok_ext;
            $cielovy_subor = "../images/products/" . $new_filename;
            
            if(move_uploaded_file($tmp_nazov, $cielovy_subor)) {
                $obrazok = $new_filename;
            } else {
                $obrazok_err = "Nastala chyba pri nahrávaní súboru.";
            }
        }
    }
    
    // Kontrola chýb pred odoslaním do databázy
    if(empty($nazov_err) && empty($popis_err) && empty($cena_err) && empty($mnozstvo_err) && empty($obrazok_err)) {
        if(isset($_POST["edit_id"]) && !empty($_POST["edit_id"])) {
            if(!empty($obrazok)) {
                $sql = "UPDATE produkty SET nazov=?, popis=?, cena=?, dostupne_mnozstvo=?, obrazok=? WHERE produkt_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssdisi", $nazov, $popis, $cena, $mnozstvo, $obrazok, $_POST["edit_id"]);
            } else {
                $sql = "UPDATE produkty SET nazov=?, popis=?, cena=?, dostupne_mnozstvo=? WHERE produkt_id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ssdii", $nazov, $popis, $cena, $mnozstvo, $_POST["edit_id"]);
            }
        }
        // Pridanie nového produktu
        else {
            $sql = "INSERT INTO produkty (nazov, popis, cena, dostupne_mnozstvo, obrazok) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdis", $nazov, $popis, $cena, $mnozstvo, $obrazok);
        }
        
        if(mysqli_stmt_execute($stmt)) {
            header("location: produkty.php?status=success");
            exit;
        } else {
            echo "Ups! Niečo sa pokazilo. Skúste to neskôr.";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Načítanie údajov produktu na úpravu
if(isset($_GET["edit"]) && !empty($_GET["edit"])) {
    $sql = "SELECT * FROM produkty WHERE produkt_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET["edit"]);
        
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $edit_id = $row["produkt_id"];
                $nazov = $row["nazov"];
                $popis = $row["popis"];
                $cena = $row["cena"];
                $mnozstvo = $row["dostupne_mnozstvo"];
                $obrazok = $row["obrazok"];
            } else {
                header("location: produkty.php");
                exit;
            }
        } else {
            echo "Ups! Niečo sa pokazilo. Skúste to neskôr.";
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkty - Admin Panel</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link href="../css/tooplate-barista.css" rel="stylesheet">
    <?php admin_css(); ?>
</head>
<body>
    <?php admin_navbar(); ?>

    <main>
        <section class="about-section section-padding" id="section_produkty">
            <div class="container-fluid">
                <div class="row">

                    <?php admin_sidebar(); ?>

                    <!-- Hlavný obsah -->
                    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="row" style="padding-top: 60px;">
                            <div class="col-12">
                                <em class="text-white">Správa produktov</em>
                                <h2 class="text-white mb-4">Produkty</h2>
                                <div class="d-flex justify-content-end mb-4">
                                    <button type="button" class="btn custom-btn custom-border-btn" data-bs-toggle="modal" data-bs-target="#produktModal">
                                        <i class="bi bi-plus-lg"></i> Pridať nový produkt
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <?php
                                if(isset($_GET["status"])) {
                                    if($_GET["status"] == "success") {
                                        echo '<div class="alert alert-success">Produkt bol úspešne uložený.</div>';
                                    } else if($_GET["status"] == "deleted") {
                                        echo '<div class="alert alert-success">Produkt bol úspešne odstránený.</div>';
                                    }
                                }
                                ?>

                                <div class="card admin-card mb-4 text-white">
                                    <div class="card-header">
                                        <i class="bi bi-box me-1"></i>
                                        Zoznam produktov
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-dark table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Obrázok</th>
                                                        <th>Názov</th>
                                                        <th>Cena</th>
                                                        <th>Na sklade</th>
                                                        <th>Akcie</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT * FROM produkty ORDER BY produkt_id DESC";
                                                    $result = mysqli_query($conn, $sql);
                                                    
                                                    if(mysqli_num_rows($result) > 0) {
                                                        while($row = mysqli_fetch_assoc($result)) {
                                                            echo "<tr>";
                                                            echo "<td>".$row['produkt_id']."</td>";
                                                            echo "<td>";
                                                            if(!empty($row['obrazok'])) {
                                                                echo '<img src="../images/products/'.$row['obrazok'].'" width="50" alt="'.$row['nazov'].'">';
                                                            } else {
                                                                echo '<img src="../images/products/default.png" width="50" alt="Default">';
                                                            }
                                                            echo "</td>";
                                                            echo "<td>".$row['nazov']."</td>";
                                                            echo "<td>".number_format($row['cena'], 2, ',', ' ')." €</td>";
                                                            echo "<td>".$row['dostupne_mnozstvo']."</td>";
                                                            echo "<td>
                                                                    <a href='produkty.php?edit=".$row['produkt_id']."' class='btn btn-sm btn-primary'><i class='bi bi-pencil'></i></a>
                                                                    <a href='produkty.php?delete=".$row['produkt_id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Naozaj chcete odstrániť tento produkt?\")'><i class='bi bi-trash'></i></a>
                                                                </td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6' class='text-center'>Žiadne produkty</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
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

    <!-- Modal pre pridanie/úpravu produktu -->
    <div class="modal fade" id="produktModal" tabindex="-1" aria-labelledby="produktModalLabel" aria-hidden="true" style="padding-top: 85px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white" id="produktModalLabel"><?php echo $edit_id ? 'Upraviť produkt' : 'Pridať nový produkt'; ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                        <div class="mb-3">
                            <label for="nazov" class="form-label">Názov produktu</label>
                            <input type="text" class="form-control bg-dark text-white <?php echo (!empty($nazov_err)) ? 'is-invalid' : ''; ?>" id="nazov" name="nazov" value="<?php echo $nazov; ?>">
                            <span class="invalid-feedback"><?php echo $nazov_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label for="popis" class="form-label">Popis produktu</label>
                            <textarea class="form-control bg-dark text-white <?php echo (!empty($popis_err)) ? 'is-invalid' : ''; ?>" id="popis" name="popis" rows="4"><?php echo $popis; ?></textarea>
                            <span class="invalid-feedback"><?php echo $popis_err; ?></span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cena" class="form-label">Cena (€)</label>
                                <input type="number" step="0.01" min="0" class="form-control bg-dark text-white <?php echo (!empty($cena_err)) ? 'is-invalid' : ''; ?>" id="cena" name="cena" value="<?php echo $cena; ?>">
                                <span class="invalid-feedback"><?php echo $cena_err; ?></span>
                            </div>
                            <div class="col-md-6">
                                <label for="mnozstvo" class="form-label">Množstvo na sklade</label>
                                <input type="number" min="0" class="form-control bg-dark text-white <?php echo (!empty($mnozstvo_err)) ? 'is-invalid' : ''; ?>" id="mnozstvo" name="mnozstvo" value="<?php echo $mnozstvo; ?>">
                                <span class="invalid-feedback"><?php echo $mnozstvo_err; ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="obrazok" class="form-label">Obrázok produktu</label>
                            <input type="file" class="form-control bg-dark text-white <?php echo (!empty($obrazok_err)) ? 'is-invalid' : ''; ?>" id="obrazok" name="obrazok">
                            <span class="invalid-feedback"><?php echo $obrazok_err; ?></span>
                            <?php if(!empty($obrazok)): ?>
                            <div class="mt-2">
                                <img src="../images/products/<?php echo $obrazok; ?>" alt="Aktuálny obrázok" class="img-thumbnail" width="100">
                                <p class="small text-muted">Aktuálny obrázok</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                            <button type="submit" class="btn custom-btn custom-border-btn"><?php echo $edit_id ? 'Upraviť' : 'Pridať'; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT FILES -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.sticky.js"></script>
    <script src="../js/custom.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if($edit_id): ?>
        // Pri úprave otvorí modal automaticky
        var produktModal = new bootstrap.Modal(document.getElementById('produktModal'));
        produktModal.show();
        <?php endif; ?>
    });
    </script>
</body>
</html>