<?php

$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$messaggio="";
if(isset($_SESSION['accesso_negato'])) $messaggio=$_SESSION['accesso_negato'];
unset($_SESSION['accesso_negato']);

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
                <h1>ACCESSO NEGATO</h1>
                <?php echo "<p>" .  htmlspecialchars($messaggio,ENT_QUOTES, 'UTF-8') . "</p>"; ?>
                <a href="login.php">Accedi</a>
            </div>
            <div class="error-image">
                <img src="../img/accesso_negato_img.svg" alt="">
            </div>
        </main>
        <div class="footer-box">
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>