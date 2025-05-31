<?php
function admin_head() {
    echo '<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-icons.css">
    <link href="../css/tooplate-barista.css" rel="stylesheet">
    <style>
    .admin-sidebar {
            margin-top: 10px;
            min-height: calc(100vh - 56px);
            background-color: rgba(0, 0, 0, 0.75);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        
        .admin-sidebar .nav-link:hover, 
        .admin-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .admin-card {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-header {
            background-color: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table {
            color: #fff;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .table td, .table th {
            border-color: rgba(255, 255, 255, 0.1);
        }
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
        </style>';
}
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
                        <a class="nav-link" href="../autentification/odhlasenie.php">Odhlásiť sa</a>
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