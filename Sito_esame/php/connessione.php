<?php
function connettiDB($ruolo){
    $host = "localhost";
    $dbname = "eco_scambio";
    $port=3306;

    if ($ruolo === "lettore") {
        $username = "lettore";
        $password = "P@ssw0rd!";
    } elseif ($ruolo === "modificatore") {
        $username = "modificatore";
        $password = "Str0ng#Admin9";
    } else {
        die("Ruolo specificato non valido");
    }

    try{
        $con = mysqli_connect($host, $username, $password, $dbname, $port);
    } catch (mysqli_sql_exception $e) {
        header('Location: errore.php');
    }


    return $con;

}
?>