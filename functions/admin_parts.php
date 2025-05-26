<?php
function admin_navbar() {
    echo '<nav class="navbar navbar-expand-lg navbar-dark py-2 fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">Zobraziť stránku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Odhlásiť sa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
}

function admin_sidebar() {
    $current_page = basename($_SERVER['PHP_SELF']);
    echo '<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse admin-sidebar" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">
                <div class="position-sticky" style="top: 100px;">
                    <ul class="nav flex-column pt-3">
                        <li class="nav-item">
                            <a class="nav-link ' . ($current_page == 'index.php' ? 'active' : '') . '" href="index.php">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ' . ($current_page == 'produkty.php' ? 'active' : '') . '" href="produkty.php">
                                <i class="bi bi-box"></i>
                                Produkty
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ' . ($current_page == 'objednavky.php' ? 'active' : '') . '" href="objednavky.php">
                                <i class="bi bi-cart"></i>
                                Objednávky
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link ' . ($current_page == 'pouzivatelia.php' ? 'active' : '') . '" href="pouzivatelia.php">
                                <i class="bi bi-people"></i>
                                Používatelia
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>';
}
?>