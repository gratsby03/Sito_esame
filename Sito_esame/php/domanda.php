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

//Gestione del filtro data
if (isset($_SESSION['filtro_data'])) {
    $data = $_SESSION['filtro_data'];
} else {
    $data = '0001-01-01';
}


//Gestione del passaggio dei materiali selezionati alla pagina di conferma
$selezionati=[];
$quantitaSel=[];

if(isset($_SESSION['selezionati'])) $selezionati = $_SESSION['selezionati'];
unset($_SESSION['selezionati']);
if(isset($_SESSION['quantita'])) $quantitaSel = $_SESSION['quantita'];
unset($_SESSION['quantita']);

include("connessione.php");

//Gestione errori
$errori = [];

if (isset($_SESSION['errori'])) {
    $errori=$_SESSION['errori'];
    unset($_SESSION['errori']);
}


$tabella = [];
$con = connettiDB("modificatore");

$stmt = mysqli_prepare($con, "SELECT ID,NOME,DESCRIZIONE,DATA,QUANTITA,COSTO FROM MATERIALI WHERE DATA > ? ORDER BY data DESC");
if (!$stmt) {
    $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
    header('Location: errore.php');
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $data);
if (!mysqli_stmt_execute($stmt)) {
    $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
    mysqli_stmt_close($stmt);
    header('Location: errore.php');
    exit;
}
mysqli_stmt_bind_result($stmt, $id,$nome,$descrizione,$data,$quantita,$costo);

$i=0;
while(mysqli_stmt_fetch($stmt)){
    $tabella[] = [$id,$nome,$descrizione,$data,$quantita,$costo];
    $i++;
}

mysqli_stmt_close($stmt);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Gestione del filtro data
    if ($_POST['form_id'] === 'filtro-data') {
        if($_POST['bottone_filtro']==="Invia"){
            $_SESSION['filtro_data'] = $_POST['filtro'];
        } else {
            unset($_SESSION['filtro_data']);
        }
        header('Location: domanda.php');
        exit;
    }

    if ($_POST['form_id'] === 'selezionati'){

        //Gestione errori lato server della quantità passata attraverso il form
        if (!empty($_POST['seleziona']) && !empty($_POST['quantita'])) {

            $selezionatiCheck = $_POST['seleziona'];
            $quantitaCheck = $_POST['quantita'];

            $ids = [];
            foreach ($selezionatiCheck as $v) $ids[] = (int)$v;

            $quantita = [];

            foreach ($ids as $id) {
                if (isset($quantitaCheck[$id])) {
                    $valore = $quantitaCheck[$id];
                } else {
                    $valore = '';
                }

                if ($valore === '') {
                    $_SESSION['errori']['conferma'] = 'Inserire una quantità valida';
                    continue;
                }

                $q = (int)$valore;

                if ($q < 1) {
                    $_SESSION['errori']['conferma'] = 'Inserire una quantità valida';
                    continue;
                }
            }

            if(isset($_SESSION['errori']['conferma'])){
                header('Location: domanda.php');
                exit;
            }

            $_SESSION['selezionati']=$selezionatiCheck;
            $_SESSION['quantita']=$quantitaCheck;

            header("Location: conferma.php");
            exit;

        } else {
            $_SESSION['errori']['conferma']='Nessun oggetto selezionato';
        }
    }

}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Domanda</title>
    <script  src="../js/validazione_acquisto.js" defer></script>
    <script  src="../js/blocco_quantita.js" defer></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg" href="../img/recycling.svg">
</head>
<body>
    <div class="header-box">
        <?php include("header.php") ?>
    </div>

    <main class="domanda">
        <div class="domanda-header">
            <h1>Materiali Disponibili</h1>
            <p>Seleziona i materiali che vuoi acquistare</p>
        </div>

        <div class="filtro-data">
            <form id="filtro-data" action="domanda.php" method="post"
            onsubmit="return validateFiltro()">
                <input type="hidden" name="form_id" value="filtro-data">
                <input type="date" id="filtro" name="filtro" 
                    min="1980-01-01" max=<?php echo date("Y-m-d") ;?>
                    value=<?php echo date("Y-m-d") ;?>>
                <input type="submit" name="bottone_filtro" value="Invia">
                <input type="submit" name="bottone_filtro" value="Reimposta">
            </form>
        </div>

        <div>
            <p class="error-quantity"><?php if(isset($errori['conferma'])) echo htmlspecialchars($errori['conferma'],ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <div class="lista-materiali">
            <form id="acquista" action="domanda.php" method="post"
            onsubmit="return validateAcquisto()">
                <input type="hidden" name="form_id" value="selezionati">
                <table>
                    <thead>
                        <tr>
                        <th>Id</th>
                        <th>Nome</th>
                        <th>Descrizione</th>
                        <th>Data</th>
                        <th>Disponibili</th>
                        <th>Costo</th>
                        <th>Seleziona</th>
                        <th>Quantità</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i=0;$i<count($tabella);$i++){
                                $id   = (int)$tabella[$i][0];
                                $maxQ = (int)$tabella[$i][4];
                                $valq = "";
                                if (isset($quantitaSel[$id])) {
                                    $q = $quantitaSel[$id];
                                    if ($q !== '' && is_numeric($q) && (int)$q >= 1) {
                                        $valq = (int)$q; 
                                    } else {
                                        $valq = '';
                                    }
                                }
                                $checked = "";
                                if(in_array((string)$id, $selezionati, true)) $checked="checked";
                                echo '<tr>';
                                echo '<td>' . $id . '</td>';
                                echo '<td>' . $tabella[$i][1] . '</td>';
                                echo '<td>' . $tabella[$i][2] . '</td>';
                                echo '<td>' . $tabella[$i][3] . '</td>';
                                echo '<td>' . $tabella[$i][4] . '</td>';
                                echo '<td>' . $tabella[$i][5] . '</td>';
                                echo '<td class="cella-checkbox"><input type="checkbox" name="seleziona[]" id="seleziona-' . $id . '" value="' . $id . '" ' . $checked . '></td>';
                                echo '<td><input type="number" name="quantita[' . $id . ']" id="quantita-' . $id . '" min="1" max="' . $maxQ . '" step="1" value="' . $valq . '"></td>';
                            }
                        ?>
                    </tbody>
                </table>
                <div class="bottoni-domanda">
                    <button type="submit" name="Conferma">Conferma</button>
                    <button type="reset" name="cancella">Cancella</button>
                </div>
            </form>
        </div>
    </main>

    <div class="footer-box">
        <?php include("footer.php"); ?>
    </div>

</body>
</html>