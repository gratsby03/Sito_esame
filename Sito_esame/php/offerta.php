<?php 
$session = true;
if (session_status() === PHP_SESSION_DISABLED) {
    $session = false;
} elseif (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include("connessione.php");
include("validazioni.php");

$errori = [];

if (isset($_SESSION['errori'])) {
    $errori=$_SESSION['errori'];
    unset($_SESSION['errori']);
}


$tabella = [];

//Redirect alla pagina di accesso negato
if(!isset($_SESSION['artigiano']) || $_SESSION['artigiano']===1){
    
    header('Location: accesso_negato.php');
    exit;
}

$con = connettiDB("modificatore");

$id_azienda = $_SESSION['id_utente'];  

$stmt = mysqli_prepare($con, "SELECT ID,NOME,DESCRIZIONE,DATA,QUANTITA,COSTO FROM MATERIALI WHERE ID_UTENTE = ?");
if (!$stmt) {
    header('Location: errore.php');
    exit;
}

mysqli_stmt_bind_param($stmt, "i",$id_azienda);
if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: errore.php');
    exit;
}
mysqli_stmt_bind_result($stmt,$id,$nome,$descrizione,$data,$quantita,$costo);

$i=0;
while(mysqli_stmt_fetch($stmt)){
    $tabella[] = [$id,$nome,$descrizione,$data,$quantita,$costo];
    $i++;
}

mysqli_stmt_close($stmt);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if($_POST['form_id'] === "aggiungi_materiale"){
        $nome = $_POST['name'];
        $descrizione = $_POST['description'];
        $data = $_POST['date'];
        $quantita = $_POST['quantity'];
        $costo = $_POST['cost'];

        $_SESSION['errori']=validazioneOfferta($nome,$descrizione,$data,$quantita,$costo);
        if($data !== "" && !validateDate($data)) $_SESSION['errori']['Data']= "Data non valida";
        if($costo !== "" && !validateEuro($costo)) $_SESSION['errori']['Costo']= "Costo non valido";

        if (!empty($_SESSION['errori'])) {
            header('Location: offerta.php');
            exit;
        }

        $costo=(float)$costo;
        $quantita=(int)$quantita;
        
        $stmt = mysqli_prepare($con, "INSERT INTO materiali (nome,descrizione,data,quantita,costo,id_utente) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }
        
        mysqli_stmt_bind_param($stmt, "sssidi", $nome, $descrizione, $data, $quantita, $costo, $id_azienda);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        header('Location: offerta.php');
    }
    
    if($_POST['form_id'] === "modifica_materiale"){
        $idMateriale = (int)$_POST['id'];
        $nome = $_POST['name_mod'];
        $descrizione = $_POST['description_mod'];
        $data = $_POST['date_mod'];
        $quantita = $_POST['quantity_mod'];
        $costo = $_POST['cost_mod'];

        $_SESSION['errori']=validazioneOfferta($nome,$descrizione,$data,$quantita,$costo);
        if($data !== "" && !validateDate($data)) $_SESSION['errori']['Data']= "Data non valida";
        if($costo !== "" && !validateEuro($costo)) $_SESSION['errori']['Costo']= "Costo non valido";

        if (!empty($_SESSION['errori'])) {
            header('Location: offerta.php');
            exit;
        }

        $costo=(float)$costo;
        $quantita=(int)$quantita;

        $stmt = mysqli_prepare($con, "UPDATE materiali SET nome = ?, descrizione = ?,data = ?, quantita = ?, costo = ? WHERE id = ? AND id_utente = ?");
        if (!$stmt) {
            header('Location: errore.php');
            exit;
        }

        mysqli_stmt_bind_param($stmt, "sssidii",$nome , $descrizione,$data, $quantita, $costo, $idMateriale, $id_azienda);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: errore.php');
            exit;
        }
        header('Location: offerta.php');
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="it">
    <head><meta charset="UTF-8">
    <title>Offerta</title>
    <script type="module" src="../js/validazione_offerta.js" defer></script>
    <script src="../js/bottoni_offerta.js" defer></script>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/svg" href="../img/recycling.svg">
</head>
<body>
    <div class="header-box">
        <?php include("header.php") ?>
    </div>
    <main class="offerta">
        <div class="lista-offerta" id="overlay-offerta">
            <div class="titolo-offerta">
                <h1>La tua offerta</h1>
                <p>Dai un'occhiata ai materiali che offri o aggiungine di nuovi</p>
            </div>
            <div class="lista-offerti">
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nome</th>
                            <th>Descrizione</th>
                            <th>Data</th>
                            <th>Quantità</th>
                            <th>Costo</th>
                            <th>Modifica</th>
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
                                echo '<td>' . $tabella[$i][4] . '</td>';
                                echo '<td>' . $tabella[$i][5] . '</td>';
                                echo '<td><button type="button" class="btn-modifica"
                                data-id="'.$tabella[$i][0] .'"
                                data-nome="'.$tabella[$i][1] .'"
                                data-descrizione="'.$tabella[$i][2].'"
                                data-data="'.$tabella[$i][3].'"
                                data-quantita="'.$tabella[$i][4].'"
                                data-costo="'.$tabella[$i][5].'">Modifica</button></td>';
                            }
                        ?>
                        <tr>
                            <td colspan="7" class="riga-bottone"><button type="button" class="btn-aggiungi" id="bottone-aggiunta">Aggiungi<div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-offerta-container" id="overlay-aggiunta">
            <div class="form-aggiunta">
                <h2>Aggiungi Nuovo Prodotto</h2>
                <form id="aggiungi_materiale" action="offerta.php" method="post"
                onsubmit="return validateOfferta(name.value,description.value,date.value,quantity.value,cost.value)">
                    <div>
                        <input type="hidden" name="form_id" value="aggiungi_materiale">
                    </div>
                    <div class="o-text">
                        <input type="text" name="name" maxlength="40" id="name" placeholder=" ">
                        <label for="name">Nome Prodotto</label>
                        <?php
                            if(!isset($errori['Nome'])){
                                echo "<p>Da 10 a 40 caratteri, lettere spazi e numeri ammessi</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Nome'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="description" maxlength="250" id="descrizione" placeholder=" ">
                        <label for="descrizione">Descrizione</label>
                        <?php
                            if(!isset($errori['Descrizione'])){
                                echo "<p>Massimo 250 caratteri</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Descrizione'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="date" maxlength="10" id="date" placeholder=" ">
                        <label for="date">Data</label>
                        <?php
                            if(!isset($errori['Data'])){
                                echo "<p>Nel formato aaaa-mm-gg</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Data'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="quantity" id="quantity" placeholder=" ">
                        <label for="quantity">Quantità</label>
                        <?php
                            if(!isset($errori['Quantita'])){
                                echo "<p>Un numero intero</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Quantita'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="cost" id="cost" placeholder=" ">
                        <label for="cost">Costo</label>
                        <?php
                            if(!isset($errori['Costo'])){
                                echo "<p>I centesimi devono essere in multipli di 5</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Costo'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-buttons">
                        <button type="submit" name="aggiungi" value="Aggiungi">Aggiungi</button>
                        <button type="reset" name="cancella" value="Cancella">Cancella</button>
                    </div>
                </form>
            </div>
            <div class="close-button">
                <button type="button" class="btn-chiudi" id="bottone-chiusura1">
                    <img src="../img/close_icon.svg" alt="chiudi">
                </button>
            </div>
        </div>
        <div class="form-offerta-container" id="overlay-modifica">
            <div class="form-aggiunta">
                <h2>Modifica il prodotto</h2>
                <form id="modifica_materiale" action="offerta.php" method="post"
                onsubmit="return validateOffertaM(name_mod.value,description_mod.value,date_mod.value,quantity_mod.value,cost_mod.value)">
                    <div>
                        <input type="hidden" name="form_id" value="modifica_materiale">
                        <input type="hidden" name="id" id="id">
                    </div>
                    <div class="o-text">
                        <input type="text" name="name_mod" maxlength="40" id="name-mod" placeholder=" ">
                        <label for="name-mod">Nome Prodotto</label>
                        <?php
                            if(!isset($errori['Nome'])){
                                echo "<p>Da 10 a 40 caratteri, lettere spazi e numeri ammessi</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Nome'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="description_mod" maxlength="250" id="descrizione-mod" placeholder=" ">
                        <label for="descrizione-mod">Descrizione</label>
                        <?php
                            if(!isset($errori['Descrizione'])){
                                echo "<p>Massimo 250 caratteri</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Descrizione'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="date_mod" maxlength="10" id="date-mod" placeholder=" ">
                        <label for="date-mod">Data</label>
                        <?php
                            if(!isset($errori['Data'])){
                                echo "<p>Nel formato aaaa-mm-gg</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Data'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="quantity_mod" id="quantity-mod" placeholder=" ">
                        <label for="quantity-mod">Quantità</label>
                        <?php
                            if(!isset($errori['Quantita'])){
                                echo "<p>Un numero intero</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Quantita'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-text">
                        <input type="text" name="cost_mod" id="cost-mod" placeholder=" ">
                        <label for="cost-mod">Costo</label>
                        <?php
                            if(!isset($errori['Costo'])){
                                echo "<p>I centesimi devono essere in multipli di 5</p>";
                            } else {
                                echo "<p>" . htmlspecialchars($errori['Costo'],ENT_QUOTES, 'UTF-8') . "</p>";
                            }
                        ?>
                    </div>
                    <div class="o-buttons">
                        <button type="submit" name="Modifica" value="Modifica">Modifica</button>
                        <button type="reset" name="cancella" value="Cancella">Cancella</button>
                    </div>
                </form>
            </div>
            <div class="close-button">
                <button type="button" class="btn-chiudi" id="bottone-chiusura2">
                    <img src="../img/close_icon.svg" alt="chiudi">
                </button>
            </div>
        </div>
    </main>

    <div class="footer-box">
        <?php include("footer.php"); ?>
    </div>
</body>
</html>