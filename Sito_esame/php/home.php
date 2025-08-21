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
        <title>Homepage</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="icon" type="image/svg" href="../img/recycling.svg">
    </head>
    <body>
        <div class="header-box">
                <?php include("header.php") ?>
        </div>
        <main class="home">
            <div class="introduzione">
                <div class="testo-introduzione">
                    <div class="logo-container">
                        <img src="../img/logo.svg" alt="cyclink">
                    </div>
                    <p>Trasformiamo materiali dimenticati in nuove opportunità, mettendo in contatto aziende e artigiani in un ecosistema sostenibile.</p>
                </div>
                <div class="immagine-introduzione">
                    <img src="../img/homeImgdef.svg" alt="">
                </div>
            </div> 

            <div class="spiegazione">
                <div class="immagine-spiegazione">
                    <img src="../img/image_spiegazione.svg" alt="">
                </div>
                <div class="contenuto-spiegazione">
                    <div class="card">
                        <h2>Perché nasce CycLink</h2>
                        <p>
                            Ogni anno tonnellate di tessuti, plastica, carta e materiali compositi vengono scartati dalle aziende e finiscono in discarica. Allo stesso tempo, artigiani, designer e startup cercano materie prime accessibili per i loro progetti.
                            CycLink nasce per ridurre questa distanza: le aziende possono caricare i materiali non più utilizzati e rimetterli in circolo, mentre gli artigiani e i creativi possono trovare risorse uniche a costi sostenibili. Un luogo semplice e sicuro, dove il riciclo diventa innovazione.
                        </p>
                    </div>
                </div>
            </div>

            <div class="redirect-home">
                <div class="header-redirect">
                    <h2>Unisciti a CycLink</h2>
                    <p>Sei pronto a dare nuova vita ai materiali? Accedi a CycLink e inizia oggi stesso a creare valore condiviso.</p>
                </div>
                <div class="container-redirect">
                    <div class="container-card">
                        <div class="card-redirect">
                            <div class="image-container">
                                <img src="../img/company_logo.svg" alt="">
                            </div>
                            <a href="registrazione.php">Registrati</a>
                            <p>Registrati come azienda o come artigiano per entrare nel mondo di Cyclink</p> 
                        </div>
                    </div>
                    <div class="container-card">
                        <div class="card-redirect">
                            <div class="image-container">
                                <img src="../img/artisan_logo.svg" alt="">
                            </div>
                            <a href="login.php">Login</a>
                            <p>Accedi per riprendere a vendere o acquistare materiali riciclati</p> 
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <div class="footer-box">
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>
