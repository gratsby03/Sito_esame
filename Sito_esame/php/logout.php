<?php
//Gestione del logout dell'utente

// Avvio della sessione
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Distruggi tutte le variabili di sessione
$_SESSION = array();

// Distruggi il cookie di sessione se esiste
$params = session_get_cookie_params();
setcookie(session_name(), '', time()-42000, 
    $params["path"], 
    $params["domain"], 
    $params["secure"],
    $params["httponly"]
);

// Rimuovi anche i cookie "ricordami" se esistono
setcookie("ultimo_username", "", time() - 3600, "/", "", true, true);
setcookie("ultima_password", "", time() - 3600, "/", "", true, true);

// Distruggi la sessione
session_destroy();

// Redirect alla home o pagina di login
header("Location: home.php");
exit;
?>