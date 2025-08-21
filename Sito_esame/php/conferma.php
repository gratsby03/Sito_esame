<?php 
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//Redirect alla pagina di accesso negato
if(!isset($_SESSION['artigiano']) || $_SESSION['artigiano']===0){
    $_SESSION['accesso_negato']="Questa pagina è riservata agli artigiani registrati";
    header('Location: accesso_negato.php');
    exit;
}

include ('connessione.php');

$con = connettiDB("modificatore");

$numeroSelezionati = count($_SESSION['selezionati']);
$valoriQuery = implode(',', array_fill(0, $numeroSelezionati, '?'));
$quantitaRimanente=[];

$stmt = mysqli_prepare($con, "SELECT ID,NOME,DESCRIZIONE,DATA,QUANTITA,COSTO FROM MATERIALI WHERE ID IN ($valoriQuery)");
if (!$stmt) {
    $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
    header('Location: errore.php');
    exit;
}

$tipi = str_repeat('i', $numeroSelezionati);
mysqli_stmt_bind_param($stmt, $tipi , ...$_SESSION['selezionati']);
if (!mysqli_stmt_execute($stmt)) {
    $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
    mysqli_stmt_close($stmt);
    header('Location: errore.php');
    exit;
}
mysqli_stmt_bind_result($stmt, $id,$nome,$descrizione,$data,$quantita,$costo);

$i=0;
$costoTotale=0;
while(mysqli_stmt_fetch($stmt)){
    $tabella[] = [$id,$nome,$descrizione,$data,$quantita,$costo];
    $costoTotale=$costoTotale + $costo*$_SESSION['quantita'][$tabella[$i][0]];
    $quantitaRimanente[$tabella[$i][0]] = $tabella[$i][4]-$_SESSION['quantita'][$tabella[$i][0]];
    $i++;
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Aggiornamento dei dati nel database se si preme conferma
    if ($_POST['azione'] === 'Conferma'){
        $stmt = mysqli_prepare($con, "UPDATE DATI_ARTIGIANI SET CREDIT=? WHERE ID_UTENTE=?");
        if (!$stmt) {
            $_SESSION['error_message'] = "Errore preparazione query UTENTI: " . mysqli_error($con);
            header('Location: errore.php');
            exit;
        }

        $_SESSION['saldo'] = ($_SESSION['saldo']-$costoTotale);
        mysqli_stmt_bind_param($stmt, "is", $_SESSION['saldo'], $_SESSION['id_utente']);
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['error_message'] = "Errore esecuzione query UTENTI: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_close($stmt);

        for($i=0;$i<$numeroSelezionati;$i++){

            $idTemp=$_SESSION['selezionati'][$i];
            if($quantitaRimanente[$idTemp]===0){
                $stmt = mysqli_prepare($con, "DELETE FROM MATERIALI WHERE ID=?");
                if (!$stmt) {
                    $_SESSION['error_message'] = "Errore preparazione query UTENTI: " . mysqli_error($con);
                    header('Location: errore.php');
                    exit;
                }

                mysqli_stmt_bind_param($stmt, "i", $idTemp );
                if (!mysqli_stmt_execute($stmt)) {
                    $_SESSION['error_message'] = "Errore esecuzione query UTENTI: " . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);
                    header('Location: errore.php');
                    exit;
                }

                mysqli_stmt_close($stmt);
            } else {
                $stmt = mysqli_prepare($con, "UPDATE MATERIALI SET QUANTITA=? WHERE ID=?");
                if (!$stmt) {
                    $_SESSION['error_message'] = "Errore preparazione query UTENTI: " . mysqli_error($con);
                    header('Location: errore.php');
                    exit;
                }

                mysqli_stmt_bind_param($stmt, "ii",$quantitaRimanente[$idTemp] ,$idTemp );
                if (!mysqli_stmt_execute($stmt)) {
                    $_SESSION['error_message'] = "Errore esecuzione query UTENTI: " . mysqli_stmt_error($stmt);
                    mysqli_stmt_close($stmt);
                    header('Location: errore.php');
                    exit;
                }
                mysqli_stmt_close($stmt);
            }

        }

        unset($_SESSION['selezionati']);
        unset($_SESSION['quantita']);
        header('Location: fine.php');

    } else if($_POST['azione'] === 'Indietro') {
        
        header('Location: domanda.php');
        exit;

    } else if($_POST['azione'] === 'Cancella') {

        unset($_SESSION['selezionati']);
        unset($_SESSION['quantita']);
        header('Location: domanda.php');
        exit;

    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Conferma</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg" href="../img/recycling.svg">
</head>
<body>
    <div class="header-box">
        <?php include("header.php") ?>
    </div>
    <main class="conferma">
        <div class="conferma-header">
            <h1>Riepilogo acquisto</h1>
            <?php
                if($costoTotale>$_SESSION['saldo']){
                    echo "<p>Il costo dei materiali selezionati è superiore al credito dell'account, premere il tasto indietro per modificare l'ordine</p>";
                }
            ?>
        </div>
        <div class="lista-materiali">
            <table>
                <thead>
                    <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Data</th>
                    <th>Quantità</th>
                    <th>Totale</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                        for($i=0;$i<count($tabella);$i++){
                            $quantitaSelezionata=$_SESSION['quantita'][$tabella[$i][0]];
                            echo '<tr>';
                            echo '<td>' . $tabella[$i][0] . '</td>';
                            echo '<td>' . $tabella[$i][1] . '</td>';
                            echo '<td>' . $tabella[$i][2] . '</td>';
                            echo '<td>' . $tabella[$i][3] . '</td>';
                            echo '<td>' . $quantitaSelezionata . '</td>';
                            echo '<td>' . $quantitaSelezionata*$tabella[$i][5] . '</td>';
                        }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Totale</td>
                        <td><?php echo $costoTotale; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="bottoni-conferma">
            <form action="conferma.php" method="post">
                <?php
                    //Gestione dei bottoni in base al costo
                    if($costoTotale>$_SESSION['saldo']){
                        echo '<button type="submit" name="azione" value="Indietro">Indietro</button>';
                    } else {
                        echo '<button type="submit" name="azione" value="Conferma">Conferma</button>';
                    }
                ?>
            <button type="submit" name="azione" value="Cancella" id="Cancella">Cancella</button>
            </form>
        </div>
    </main>
    <div class="footer-box">
        <?php include("footer.php"); ?>
    </div>
</body>
</html>