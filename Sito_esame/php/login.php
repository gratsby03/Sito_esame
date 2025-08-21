<?php
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//reindirizzamento se si è già autenticati

include("connessione.php");

// Gestione degli errori: vengono visualizzati e subito dopo rimossi dalla sessione per non essere più visualizzati dopo un refresh
$errori = [];

if (isset($_SESSION['errori'])) {
    $errori=$_SESSION['errori'];
    unset($_SESSION['errori']);
}

//Riempimento automatico dei campi del login se i cookie sono attivi come da traccia
if(isset($_COOKIE['ultimo_username'])){
    $ultimoUsername=$_COOKIE['ultimo_username'];
} else{
    $ultimoUsername = "";
}

if(isset($_COOKIE['ultimo_username'])){
    $ultimaPassword = $_COOKIE['ultima_password'];
} else{
    $ultimaPassword = "";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ricordami = isset($_POST['ricordami']);

    $regUsername = '/^[A-Za-z][A-Za-z0-9_-]{3,9}$/';
    $regPassword = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.;+=])[[:alnum:][:punct:]]{8,16}$/';

    if($username === "") $_SESSION['errori']['Username'] = "Inserire username";
    elseif(!preg_match($regUsername,$username)) $_SESSION['errori']['Username'] = "Username non valido";
    if($password === "") $_SESSION['errori']['Password']= "Inserire Password";
    elseif( !preg_match($regPassword,$password) )  $_SESSION['errori']['Password'] = "Password non valida";
         
    if (isset($_SESSION['errori'])) {
        header('Location: login.php');
        exit;
    }
    
    $con = connettiDB("lettore");
    
    $stmt = mysqli_prepare($con, "SELECT id, password, artigiano FROM utenti WHERE nick = ?");
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

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $id_utente, $password_db, $artigiano);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($password === $password_db) {
            // LOGIN RIUSCITO
            $_SESSION['id_utente'] = $id_utente;
            $_SESSION['username'] = $username;
            $_SESSION['artigiano'] = $artigiano;

            // Carica il credito se artigiano
            if ($artigiano) {
                $stmt2 = mysqli_prepare($con, "SELECT credit FROM dati_artigiani WHERE id_utente = ?");
                if (!$stmt2) {
                    header('Location: errore.php');
                    exit;
                }
                
                mysqli_stmt_bind_param($stmt2, "i", $id_utente);
                if (!mysqli_stmt_execute($stmt2)) {
                    mysqli_stmt_close($stmt);
                    header('Location: errore.php');
                    exit;
                }
                mysqli_stmt_bind_result($stmt2, $credito);
                mysqli_stmt_fetch($stmt2);
                $_SESSION['saldo'] = $credito;
                mysqli_stmt_close($stmt2);
            }

            mysqli_close($con);

            if ($ricordami) {
                setcookie("ultimo_username", $username,  time() + 72 * 3600, "/", "", true, true);
                setcookie("ultima_password", $password, time() + 72 * 3600, "/", "", true, true);
            } else {
                setcookie("ultimo_username", "", time() - 3600, "/", "", true, true);
                setcookie("ultima_password", "", time() - 3600, "/", "", true, true);
            }

            // REDIRECT DOPO LOGIN RIUSCITO
            if($artigiano===1) header('Location: domanda.php');
            else header('Location: offerta.php');
            exit;
        } else {
            $_SESSION['errori']['Password']="Password errata";
            header('Location: login.php');
        }
    } else {
        $_SESSION['errori']['Username']="Utente non esistente";
        header('Location: login.php');
    }
}

?>

<!-- Blocco di codice per il form -->
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="../js/validazione_login.js" defer></script>
    <script src="../js/account_button.js" defer></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg" href="../img/recycling.svg">
</head>
<body>
    <div class="header-box">
        <?php include("header.php") ?>
    </div>
    <main class="login">
        <div class="login-header">
            <h1>Bentornato</h1>
            <p>Accedi ed inizia a scambiare materiali</p>
        </div> 
        <form class="login-form" name="login" action="login.php" method="post"
        onsubmit="return validateLogin(username.value,password.value);">
            <div class="login-text">
                <input type="text" name="username" id="username" maxlength="10" placeholder=" " value="<?php echo htmlspecialchars($ultimoUsername, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="username" class="<?php if(isset($errori['Username'])) echo 'error';?>"><?php
                                                                                                                if((isset($errori['Username']))) echo htmlspecialchars($errori['Username'], ENT_QUOTES, 'UTF-8');
                                                                                                                else echo 'Username';
                                                                                                                ?></label>
            </div>
            <div class="login-text">
                <input type="password" name="password" id="password" maxlength="16" placeholder=" " value="<?php echo htmlspecialchars($ultimaPassword, ENT_QUOTES, 'UTF-8'); ?>">
                <label for="password" class="<?php if(isset($errori['Password'])) echo 'error';?>"><?php
                                                                                                                if((isset($errori['Password']))) echo htmlspecialchars($errori['Password'], ENT_QUOTES, 'UTF-8');
                                                                                                                else echo 'Password';
                                                                                                                ?></label>
            </div>
            <div class="ricordami">
                <input type="checkbox" name="ricordami" id="ricordami">
                <label for="ricordami">Ricordami</label>
            </div>
            <div class="login-bottoni">
                <button type="submit" name="invia" value="Invia">Invia</button>
                <button type="reset" name="cancella" value="Cancella">Cancella</button>
            </div>
        </form>
    </main>
    <div class="footer-box">
        <?php include("footer.php") ?>
    </div>
</body>
</html>