<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Effacer le cookie de session s'il existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 86400, '/');
}

// Détruire la session
session_destroy();

// Rediriger vers test-1.php
header("Location: test-1.php");
exit();
?>
