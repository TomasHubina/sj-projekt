<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span>Gold Coffee</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Domov</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#section_2">O nás</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#barista-team">Náš tím</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#section_3">Naša káva</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#section_4">Referencie</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php#section_5">Kontakt</a>
                </li>
            </ul>

            <div class="d-lg-flex align-items-center">
                <?php
                if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <div class="dropdown">
                        <button class="btn custom-btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION["meno"]); ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <?php if (isset($_SESSION["je_admin"]) && $_SESSION["je_admin"] == 1): ?>
                                <li><a class="dropdown-item" href="admin/index.php">Admin panel</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="autentification/odhlasenie.php">Odhlásiť sa</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn custom-btn" href="autentification/prihlasenie.php">
                        <i class="bi bi-person"></i> Prihlásenie
                    </a>
                <?php endif; ?>
                
                <a class="btn custom-btn custom-border-btn ms-2" href="kosik.php">
                    <i class="bi bi-cart"></i> Košík
                    <?php
                    if (isset($_SESSION['kosik']) && is_array($_SESSION['kosik']) && count($_SESSION['kosik']) > 0): ?>
                        <span class="badge bg-dark rounded-pill ms-1"><?php echo count($_SESSION['kosik']); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>