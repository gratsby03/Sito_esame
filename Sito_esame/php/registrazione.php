<?php
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//Se l'utente è già autenticato la pagina di registrazione rimanda direttamente a offerta o domanda a seconda del tipo di utente
if(isset($_SESSION['artigiano'])){
    if($_SESSION['artigiano']===1){
        header('Location: domanda.php');
        exit;
    } else {
        header('Location: offerta.php');
        exit;
    }
}

include("connessione.php");
include("validazioni.php");


//Gestione del radio button selezionato dopo un errore, di default è settato su azienda, ma se l'errore proviene dal form artigiano la pagina ricaricherà direttamente col form artigiano
$checked='azienda';
if(isset($_SESSION['checked'])) $checked=$_SESSION['checked'];
unset($_SESSION['checked']);

// Gestione degli errori analoga a quanto fatto in login.php
$errori = [];

if (isset($_SESSION['errori'])) {
    $errori=$_SESSION['errori'];
    unset($_SESSION['errori']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $con=connettiDB('modificatore');
    
    //controllo valore di artigiano anche se l'input è di tipo hidden l'utente potrebbe effettuare modifiche
    if ($_POST['artigiano'] !== "0" && $_POST['artigiano'] !== "1") {
        header('Location: registrazione.php');
        exit;
    } else {
        $artigiano = (int) $_POST['artigiano'];
    }

    // Gestione del form di registrazione per l'artigiano
    if($artigiano===1){

        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $birthdate = $_POST['birthdate'];
        $credito = (float)$_POST['credit'];
        $indirizzo = $_POST['address'];
        $username = $_POST['nick_art'];
        $password = $_POST['password_art'];
        $password2 = $_POST['password_art2'];
        $listaCampi = [$name,$surname,$birthdate,$credito,$indirizzo,$username,$password,$password2];

        $regName='/^[A-Za-z\s]{4,14}$/';
        $regSurname='/^[A-Za-z\s\']{4,16}$/';
        $regBirthdate='/^(\d{4})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/';
        $regCredit='/^\d+\.\d{2}$/';
        $regAddress='/^(Via|Corso)\s+[A-Za-z\s]+\s+\d{1,3},\s+[A-Za-z\s]+$/';
        $regUsername = '/^[A-Za-z][A-Za-z0-9_-]{3,9}$/';
        $regPassword = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.;+=])[[:alnum:][:punct:]]{8,16}$/';
        $listaRegexp = [$regName,$regSurname,$regBirthdate,$regCredit,$regAddress,$regUsername,$regPassword,$regPassword];
        $listaNomi = ['Nome','Cognome','Data di nascita', 'Credito', 'Indirizzo', 'Username', 'Password', 'Conferma password'];

        for($i=0;$i<count($listaCampi);$i++){
            if($listaCampi[$i]==="") $_SESSION['errori'][$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " mancante";
            elseif(!preg_match($listaRegexp[$i],$listaCampi[$i])){
                $_SESSION['errori'][$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " non valido";
            }
        }

        if($birthdate!=="" && !validateDate($birthdate)) $_SESSION['errori']['Data di nascita']= "Data di nascita non valida";
        if($credito!=="" && !validateEuro($credito)) $_SESSION['errori']['Credito']= "Credito non valido";
        if($password!=="" && $password2!=="" && $password!==$password2){ 
            $_SESSION['errori']['Password']= "Le password devono coincidere";
            $_SESSION['errori']['Conferma password']= "Le password devono coincidere";
        }

        if (isset($_SESSION['errori'])) {
            $_SESSION['checked']='artigiano';
            header('Location: registrazione.php');
            exit;
        }

        $stmt = mysqli_prepare($con, "SELECT * FROM utenti WHERE nick = ?");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_close($stmt);
            $_SESSION['errori']['Username'] = "Username già esistente";
            $_SESSION['checked']='artigiano';
            header('Location: registrazione.php');
            exit;
        }

        $stmt = mysqli_prepare($con, "INSERT INTO UTENTI (NICK, PASSWORD, ARTIGIANO) VALUES (?, ?, TRUE)");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        $userId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($con, "INSERT INTO DATI_ARTIGIANI (ID_UTENTE, NAME, SURNAME, BIRTHDATE, CREDIT, ADDRESS) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "isssds", $userId, $name, $surname, $birthdate, $credito, $indirizzo);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        mysqli_stmt_close($stmt);

        $_SESSION['id_utente'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['artigiano'] = $artigiano;
        $_SESSION['saldo'] = $credito;

        header('Location: domanda.php');
        exit;

    // Gestione del form di registrazione per le aziende
    } else if($artigiano===0){
        $ragione = $_POST['ragione'];
        $indirizzo = $_POST['address2'];
        $username = $_POST['nick'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        $listaCampi = [$ragione,$indirizzo,$username,$password,$password2];

        $regRagione = '/^[A-Z][A-Za-z\d\&\s]{0,29}$/';
        $regAddress2 ='/^(Via|Corso)\s+[A-Za-z\s]+\s+\d{1,3},\s+[A-Za-z\s]+$/';
        $regUsername = '/^[A-Za-z][A-Za-z0-9_-]{3,9}$/';
        $regPassword = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.;+=])[[:alnum:][:punct:]]{8,16}$/';
        $listaRegexp = [$regRagione,$regAddress2,$regUsername,$regPassword,$regPassword];
        $listaNomi = ["Ragione sociale","Indirizzo","Username","Password","Conferma password"];

        for($i=0;$i<count($listaCampi);$i++){
            if($listaCampi[$i]==="") $_SESSION['errori'][$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " mancante";
            elseif(!preg_match($listaRegexp[$i],$listaCampi[$i])){
                $_SESSION['errori'][$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " non valido";
            }
        }

        if($password!=="" && $password2!=="" && $password!==$password2){ 
            $_SESSION['errori']['Password']= "Le password devono coincidere";
            $_SESSION['errori']['Conferma password'] = "Le password devono coincidere";
        }

        if (!empty($_SESSION['errori'])) {
            $_SESSION['checked']='azienda';
            header('Location: registrazione.php');
            exit;
        }

        $stmt = mysqli_prepare($con, "SELECT * FROM utenti WHERE nick = ?");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) { 
            mysqli_stmt_close($stmt);
            $_SESSION['errori']['Username'] = "Username già esistente";
            $_SESSION['checked']='azienda';
            header('Location: registrazione.php');
            exit;
        }

        $stmt = mysqli_prepare($con, "INSERT INTO UTENTI (NICK, PASSWORD, ARTIGIANO) VALUES (?, ?, FALSE)");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        $userId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($con, "INSERT INTO DATI_AZIENDE (ID_UTENTE, RAGIONE, ADDRESS2) VALUES (?,?,?)");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "iss", $userId, $ragione, $indirizzo);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        mysqli_stmt_close($stmt);

        $_SESSION['id_utente'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['artigiano'] = $artigiano;
        
        header('Location: offerta.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Registrazione</title>
        <script type="module" src="../js/validazione_register.js" defer></script>
        <script src="../js/radio_button.js" defer></script>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="icon" type="image/svg" href="../img/recycling.svg">
    </head>
    <body>
        <div class="header-box">
                <?php include("header.php") ?>
        </div>
        <main class="registrazione">
            <div class="registrazione-form">
                <h1>Registrati</h1>
                <div class="seleziona-tipo">
                    <p>Sei un'azienda o un artigiano?</p>
                    <div class="radio-button">
                        <div>
                            <input type="radio" name="tipo-utente" value="azienda" id="tipo-azienda" <?php if($checked==="azienda") echo 'checked'; ?> >
                            <label for="tipo-azienda">Azienda</label>
                        </div>
                        <div>
                            <input type="radio" name="tipo-utente" value="artigiano" id="tipo-artigiano" <?php if($checked==="artigiano") echo 'checked'; ?> >
                            <label for="tipo-artigiano">Artigiano</label>
                        </div>
                    </div>
                </div>
                <div class="registra-azienda">
                    <form name="registrazione azienda" action="registrazione.php" method="post"
                    onsubmit="return validateCompany(ragione.value,address2.value,nick.value,password.value,password2.value);">
                        <div class="company-text">
                            <input type="text" name="ragione" maxlength="30" id="ragione" placeholder=" ">
                            <label for="ragione">Ragione sociale</label>
                            <?php
                                if(!isset($errori['Ragione sociale'])){
                                    echo "<p>Massimo 30 caratteri</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Ragione sociale'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="company-text">
                            <input type="text" name="address2" id="address2" placeholder=" ">
                            <label for="address2">Indirizzo</label>
                            <?php
                                if(!isset($errori['Indirizzo'])){
                                    echo "<p>Formato “Via/Corso nome numeroCivico, Città”</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Indirizzo'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="company-text">
                            <input type="text" name="nick" maxlength="10" id="username" placeholder=" ">
                            <label for="username">Username</label>
                            <?php
                                if(!isset($errori['Username'])){
                                    echo "<p>Da 4 a 10 caratteri,lettere,numeri,'-','_',primo carattere alfabetico</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Username'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="company-text">
                            <input type="password" name="password" maxlength="16" id="password" placeholder=" ">
                            <label for="password">Password</label>
                            <?php
                                if(!isset($errori['Password'])){
                                    echo "<p>Da 8 a 16 caratteri,lettere,numeri,caratteri speciali,almeno uno</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Password'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="company-text">
                            <input type="password" name="password2" maxlength="16" id="password2" placeholder=" ">
                            <label for="password2">Conferma password</label>
                            <?php
                                if(!isset($errori['Conferma password'])){
                                    echo "<p></p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Conferma password'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>      
                        <div class="artigiano">
                            <input type="hidden" name="artigiano" value="0">
                        </div>             
                        <div class="registration-button">
                            <button type="submit" name="iscriviti" value="Iscriviti">Iscriviti</button>
                            <button type="reset" name="cancella" value="Cancella">Cancella</button>
                        </div>
                    </form>
                </div>

                <div class="registra-artigiano">
                    <form name="registrazione artigiano" action="registrazione.php" method="post"
                    onsubmit="return validateArtisan(name.value,surname.value,birthdate.value,credit.value,address.value,nick_art.value,password_art.value,password_art2.value);">
                        <div class="artisan-text">
                            <input type="text" name="name" maxlength="14" id="name" placeholder=" ">
                            <label for="name">Nome</label>
                            <?php
                                if(!isset($errori['Nome'])){
                                    echo "<p>Da 4 a 14 caratteri, solo lettere</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Nome'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="text" name="surname" maxlength="16" id="surname" placeholder=" ">
                            <label for="surname">Cognome</label>
                            <?php
                                if(!isset($errori['Cognome'])){
                                    echo "<p>Da 4 a 16 caratteri, solo lettere</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Cognome'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="text" name="birthdate" maxlength="10" id="birthdate" placeholder=" ">
                            <label for="birthdate">Data di nascita</label>
                            <?php
                                if(!isset($errori['Data di nascita'])){
                                    echo "<p>Nel formato aaaa-mm-gg</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Data di nascita'],ENT_QUOTES,'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="text" name="credit" id="credit" placeholder=" ">
                            <label for="credit">Credito</label>
                            <?php
                                if(!isset($errori['Credito'])){
                                    echo "<p>I centesimi devono essere in multippli di 5</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Credito'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="text" name="address" id ="address" placeholder=" ">
                            <label for="address">Indirizzo</label>
                            <?php
                                if(!isset($errori['Indirizzo'])){
                                    echo "<p>Formato “Via/Corso nome numeroCivico, Città</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Indirizzo'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="text" name="nick_art" maxlength="10" id="username_art" placeholder=" ">
                            <label for="username_art">Username</label>
                            <?php
                                if(!isset($errori['Username'])){
                                    echo "<p>Da 4 a 10 caratteri,lettere,numeri,'-','_',primo carattere alfabetico</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Username'],ENT_QUOTES ,'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="password" name="password_art" maxlength="16" id="password_art" placeholder=" ">
                            <label for="password_art">Password</label>
                            <?php
                                if(!isset($errori['Password'])){
                                    echo "<p>Da 8 a 16 caratteri,lettere,numeri,caratteri speciali,almeno uno</p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Password'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>
                        <div class="artisan-text">
                            <input type="password" name="password_art2" maxlength="16" id="password_art2" placeholder=" ">
                            <label for="password_art2">Conferma password</label>
                            <?php
                                if(!isset($errori['Conferma password'])){
                                    echo "<p></p>";
                                } else {
                                    echo "<p>" . htmlspecialchars($errori['Conferma password'],ENT_QUOTES, 'UTF-8') . "</p>";
                                }
                            ?>
                        </div>       
                        <div class="artigiano">
                            <input type="hidden" name="artigiano" value="1">
                        </div>
                        <div class="registration-button">
                            <button type="submit" name="iscriviti" value="Iscriviti">Iscriviti</button>
                            <button type="reset" name="cancella" value="Cancella">Cancella</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="registrazione-image">
                <img src="../img/registration-image.png" alt="">
            </div>
        </main>
        <div class="footer-box">
            <?php include("footer.php"); ?>
        </div>
    </body>
</html>