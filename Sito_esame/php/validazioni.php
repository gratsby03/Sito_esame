<?php
function validateDate($data){
    $parti = explode('-', $data);
    $year = (int)$parti[0];
    $month = (int)$parti[1];
    $day = (int)$parti[2];
    return checkdate($month, $day, $year);
}

function validateEuro($euro){
    $euro=(float)$euro;
    $euro=$euro*100;
    return ($euro % 5 === 0);
}

function validazioneOfferta($nome,$descrizione,$data,$quantita,$costo){

    $errori=[];

    $regNome ='/^[A-Za-z\d[:space:]]{10,40}$/';
    $regDescrizione = '/^.{0,250}$/';
    $regData = '/^(\d{4})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/';
    $regQuantita = '/^[\d]/';
    $regCosto = '/^\d+(\.\d{2})?$/';

    $listaCampi = [$nome,$descrizione,$data,$quantita,$costo];
    $listaRegexp = [$regNome,$regDescrizione,$regData,$regQuantita,$regCosto];
    $listaNomi = ['Nome', 'Descrizione', 'Data', 'Quantita', 'Costo'];

    for($i=0;$i<count($listaCampi);$i++){
        if($listaCampi[$i]==="") $errori[$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " mancante";
        elseif(!preg_match($listaRegexp[$i],$listaCampi[$i])){
            $errori[$listaNomi[$i]]= "Campo " . $listaNomi[$i] . " non valido";
        }
    }

    return $errori;
}
?>

