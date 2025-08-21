<?php 
//Avvio della sessione
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include("connessione.php");

$tabella = [];

// Determina se l'utente è autenticato e che tipo di utente è
$utente_autenticato = isset($_SESSION['id_utente']);
$is_artigiano = $utente_autenticato && $_SESSION['artigiano'] == 1;
$is_azienda = $utente_autenticato && $_SESSION['artigiano'] == 0;

$filtro_data_attivo = isset($_SESSION['filtro_data']) && !empty($_SESSION['filtro_data']);
if($filtro_data_attivo) $filtro_data = $_SESSION['filtro_data'];

if (!$utente_autenticato || $is_azienda) {
    $informazioni = false;
} else {
    $informazioni = true;
}

$con=connettiDB('lettore');

//Gestione della visuale in base al filtro data
if(!$filtro_data_attivo){
    $stmt = mysqli_prepare($con, "SELECT ID,NOME,DESCRIZIONE,DATA,QUANTITA,COSTO FROM MATERIALI ORDER BY data DESC");
    if (!$stmt) {
        $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
        header('Location: errore.php');
        exit;
    }

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


} else {
    $stmt = mysqli_prepare($con, "SELECT ID,NOME,DESCRIZIONE,DATA,QUANTITA,COSTO FROM MATERIALI WHERE DATA > ? ORDER BY data DESC");
    if (!$stmt) {
        $_SESSION['error_message'] = "Si è verificato un errore, riprovare";
        header('Location: errore.php');
        exit;
    }

    mysqli_stmt_bind_param($stmt, "s", $filtro_data);
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
}

            
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg" href="../img/recycling.svg">
</head>
<body>
    <div class="header-box">
        <?php include("header.php") ?>
    </div>
    
    <main class="lista">
        <div class="lista-header">
            <h1>Materiali Disponibili</h1>
            <?php if ($filtro_data_attivo){
                echo '<div class="filtro-lista">';
                echo '<p>Filtro attivo:Mostrati solo materiali inseriti dopo il' . htmlspecialchars($filtro_data) . '</p>';
                echo '<p>Il filtro è stato applicato dalla pagina Domanda</p>';
                echo '</div>';
            } else {
                echo '<div class="filtro-lista">';
                echo '<p>Filtro disattivo: sono mostrati tutti i materiali </p>';
                echo '<p>Il filtro è attivabile dagli artigiani nella pagina di Domanda</p>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="tabella-materiali">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nome</th>
                        <th>Descrizione</th>
                        <th>Data</th>
                        <?php 
                            if($is_artigiano){
                                echo "<th>Quantità</th>";
                                echo "<th>costo</th>";
                            }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for($i=0;$i<count($tabella);$i++){
                            echo '<tr>';
                            echo '<td>' . $tabella[$i][0] . '</td>';
                            echo '<td>' . $tabella[$i][1] . '</td>';
                            echo '<td>' . $tabella[$i][2] . '</td>';
                            echo '<td>' . $tabella[$i][3] . '</td>';
                            if($is_artigiano){
                                echo '<td>' . $tabella[$i][4] . '</td>';
                                echo '<td>' . $tabella[$i][5] . '</td>';
                            }
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <div class="footer-box">
        <?php include("footer.php"); ?>
    </div>
</body>
</html>