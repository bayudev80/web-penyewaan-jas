<?php
session_start();

// Hapus semua session
$_SESSION = [];

// Hapus cookie session (biar benar-benar logout total)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Paksa tidak cache halaman login
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect ke login
header("Location: login.php");
exit();
?>