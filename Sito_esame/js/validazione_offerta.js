"use strict";
import * as validazioni from '../js/validazione_generale.js';

function validateOfferta(name,description,date,quantity,cost){

    const regName = /^[A-Za-z\d\s]{10,40}$/;
    const regDescrizione = /^.{0,250}$/;
    const regData = /^(\d{4})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/;
    const regQuantita = /^\d+$/;
    const regCosto = /^\d+(\.\d{2})?$/;

    let listaCampi=[name, description,date,quantity,cost]
    let nomiCampi=["name","description","date","quantity","cost"]
    let listaRegexp=[regName,regDescrizione,regData,regQuantita,regCosto]
    let campiMancanti = [];
    let errore = false;
    
    for (let i=0; i<listaCampi.length;i++){
        if(listaCampi[i] === "") campiMancanti.push(nomiCampi[i]);
        else if (!listaRegexp[i].test(listaCampi[i])){
            errore=validazioni.validate(nomiCampi[i],"Non Valido");
        }
    }
    
    if(cost !== "" && !validazioni.validateEuro(cost)){
        errore=validazioni.validate("cost","Costo in multipli di 5");
    }

    if(date !== "" && !validazioni.validateDate(date)){
        errore=validazioni.validate('date', "Data non valida");
    }

    if (campiMancanti.length!==0) {
        for (let mancante of campiMancanti) {
           errore=validazioni.validate(mancante, "Campo mancante");
        }
        errore=true;
    }

    if(errore===true){
        return false;
    }

    return true;
}

function validateOffertaM(name,description,date,quantity,cost){

    const regName = /^[A-Za-z\d\s]{10,40}$/;
    const regDescrizione = /^.{0,250}$/;
    const regData = /^(\d{4})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/;
    const regQuantita = /^\d+$/;
    const regCosto = /^\d+(\.\d{2})?$/;

    let listaCampi=[name, description,date,quantity,cost]
    let nomiCampi=["name_mod","description_mod","date_mod","quantity_mod","cost_mod"]
    let listaRegexp=[regName,regDescrizione,regData,regQuantita,regCosto]
    let campiMancanti = [];
    let errore = false;
    
    for (let i=0; i<listaCampi.length;i++){
        if(listaCampi[i] === "") campiMancanti.push(nomiCampi[i]);
        else if (!listaRegexp[i].test(listaCampi[i])){
            errore=validazioni.validate(nomiCampi[i],"Non Valido");
        }
    }

    if(cost !== "" && !validazioni.validateEuro(cost)){
        errore=validazioni.validate("cost_mod","Costo in multipli di 5");
    }

    if(date !== "" && !validazioni.validateDate(date)){
        errore=validazioni.validate('date_mod', "Data non valida");
    }

    if (campiMancanti.length!==0) {
        for (let mancante of campiMancanti) {
           errore=validazioni.validate(mancante, "Campo mancante");
        }
        errore=true;
    }

    if(errore===true){
        return false;
    }

    return true;
}

window.validateOfferta = validateOfferta;
window.validateOffertaM = validateOffertaM;