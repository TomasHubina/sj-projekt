<?php
// Inicializácia session
session_start();

// Zrušenie všetkých session premenných
$_SESSION = array();

// Zrušenie session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Ukončenie session
session_destroy();

// Presmerovanie na prihlasovaciu stránku
header("location: ../index.php");
exit;
?>