<?php

$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Offerta</title>
        <script type="module" src="../js/validazione_offerta.js" defer></script>
        <script type="module" src="../js/form_modifica.js" defer></script>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="icon" type="image/svg" href="../img/recycling.svg">
    </head>
    <body>
        <div class="header-box">
            <?php include("header.php") ?>
        </div>
        <main class="errore">
            <div class="error-content">
                <h1>Complimenti</h1>
                <p>L'acquisto si è concluso con successo</p>
                <a href="home.php">Torna alla home</a>
            </div>
            <div class="error-image">
                <img src="../img/fine_image.svg" alt="">
            </div>
        </main>
        <div class="footer-box">
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>