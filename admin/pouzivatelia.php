<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../autentification/prihlasenie.php");
    exit;
}

if(!isset($_SESSION["je_admin"]) || $_SESSION["je_admin"] != 1){
    header("location: ../index.php");
    exit;
}

require_once "../db/config.php";
require_once "../functions/admin_parts.php";
require_once "../functions/jsAcss.php";
require_once "../db/model/Pouzivatel.php";

$pouzivatelia = Pouzivatel::getAll();
?>

<!doctype html>
<html lang="sk">
<?php admin_head(); ?>
<body>
    <?php admin_navbar(); ?>

    <main>
        <section class="about-section section-padding" id="section_pouzivatelia">
            <div class="container-fluid">
                <div class="row">
                    
                    <?php admin_sidebar(); ?>

                    <!-- Hlavný obsah -->
                    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                        <div class="row" style="padding-top: 60px;">
                            <div class="col-12">
                                <em class="text-white">Správa používateľov</em>
                                <h2 class="text-white mb-4">Používatelia</h2>
                            </div>
                            <div class="col-12">
                                <div class="card admin-card mb-4 text-white">
                                    <div class="card-header">
                                        <i class="bi bi-people me-1"></i> 
                                        Zoznam používateľov
                                    </div>
                                    <div class="card-body">
                                        <?php if(count($pouzivatelia) > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-dark table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Meno</th>
                                                            <th>Priezvisko</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                            <th>Dátum registrácie</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($pouzivatelia as $pouzivatel): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($pouzivatel->getId()); ?></td>
                                                                <td><?php echo htmlspecialchars($pouzivatel->getMeno()); ?></td>
                                                                <td><?php echo htmlspecialchars($pouzivatel->getPriezvisko()); ?></td>
                                                                <td><?php echo htmlspecialchars($pouzivatel->getEmail()); ?></td>
                                                                <td>
                                                                    <?php if($pouzivatel->isAdmin() == 1): ?>
                                                                        <span class="badge bg-primary">Admin</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success">Používateľ</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php 
                                                                        $datum = $pouzivatel->getDatum();
                                                                        if($datum) {
                                                                            echo date("d.m.Y H:i", strtotime($datum));
                                                                        } else {
                                                                            echo "Neznámy";
                                                                        }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-dark text-white">
                                                <p>Momentálne nemáte žiadnych registrovaných používateľov.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php js(); ?>
</body>
</html>