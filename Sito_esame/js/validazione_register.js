"use strict";
import * as validazione from "../js/validazione_generale.js";

function validateArtisan(name,surname,birthdate,credit,address,nick,password,password2){
    
    const regName=/^[A-Za-z\s]{4,14}$/;
    const regSurname=/^[A-Za-z\s']{4,16}$/;
    const regBirthdate=/^(\d{4})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/;
    const regCredit=/^\d+\.\d{2}$/;
    const regAddress=/^(Via|Corso)\s+[A-Za-z\s]+\s+\d{1,3},\s+[A-Za-z\s]+$/;
    const regUsername = /^[A-Za-z][A-Za-z0-9_-]{3,9}$/;
    const regPassword = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[\d])(?=.*[\.;+=])[A-Za-z\d\!"#$%&'()*+,\-./:;<=>?@[\\\]^_{|}~]{8,16}$/;

    let listaCampi=[name,surname,birthdate,credit,address,nick,password,password2];
    let nomiCampi=["name","surname","birthdate","credit","address","nick_art","password_art","password_art2"];
    let labels=["Nome","Cognome","Data di nascita","Credito","Indirizzo","Username","Password","Conferma Password"];
    let listaRegexp=[regName,regSurname,regBirthdate,regCredit,regAddress,regUsername,regPassword,regPassword];
    let campiMancanti = [];
    let errore = false;

    for (let i=0; i<listaCampi.length;i++){
        if(listaCampi[i] === "") campiMancanti.push(nomiCampi[i]);
        else if (!listaRegexp[i].test(listaCampi[i])){
            errore=validazione.validate(nomiCampi[i],"Non Valido");
        }
    }

    if(password !== "" && password2 !== "" && password!=password2){
        let passwordInput = document.querySelector('input[name="password_art"]');
        let passwordLabel = passwordInput.parentElement.querySelector('label');
        let password2Input = document.querySelector('input[name="password_art2"]');
        let password2Label = password2Input.parentElement.querySelector('label');

        passwordLabel.textContent = "Le password devono coincidere";
        password2Label.textContent = "Le password devono coincidere";

        passwordLabel.classList.add('error');
        passwordInput.classList.add('error');
        passwordInput.value = "";
        password2Input.value = "";
        
        passwordInput.addEventListener('input', function() {
            passwordLabel.textContent = "Password";
            password2Label.textContent = "Conferma Password";
            passwordLabel.classList.remove('error');
            passwordInput.classList.remove('error');
        }, { once: true });
        errore=true;
    }

    if(credit !== "" && !validazione.validateEuro(credit)){
        errore = validazione.validate("credit", "Credito non valido");
    }

    if(birthdate !== "" && !validazione.validateDate(birthdate)){
        errore = validazione.validate("birthdate", "Data non valida");
    }
    if (campiMancanti.length!==0) {
        for (let mancante of campiMancanti) {
           errore=validazione.validate(mancante,"Campo Obbligatorio");
        }
        errore=true;
    }

    if(errore===true){
        return false;
    }

    return true;
}

function validateCompany(ragione,address2,nick,password,password2){
    const regRagione = /^[A-Z][A-Za-z\d\&\s]{0,29}$/;
    const regAddress2 =/^(Via|Corso)\s+[A-Za-z\s]+\s+\d{1,3},\s+[A-Za-z\s]+$/;
    const regUsername = /^[A-Za-z][A-Za-z0-9_-]{3,9}$/;
    const regPassword = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[\d])(?=.*[\.;+=])[A-Za-z\d\!"#$%&'()*+,\-./:;<=>?@[\\\]^_{|}~]{8,16}$/;

    let listaRegexp = [regRagione,regAddress2,regUsername,regPassword,regPassword];
    let listaCampi = [ragione,address2,nick,password,password2];
    let nomiCampi = ["ragione", "address2", "nick", "password", "password2"];
    let labels=["Ragione","Indirizzo","Username","Password","Conferma Password"];
    let campiMancanti = [];
    let errore = false;

    for(let i=0; i<listaCampi.length;i++){
        if(listaCampi[i] === "") campiMancanti.push(nomiCampi[i]);
        else if (!listaRegexp[i].test(listaCampi[i])){
            errore=validazione.validate(nomiCampi[i],"Non Valido");
        }
    }

    if (password !== "" && password2 !== "" && password != password2){
        let passwordInput = document.querySelector('input[name="password"]');
        let passwordLabel = passwordInput.parentElement.querySelector('label');
        let password2Input = document.querySelector('input[name="password2"]');
        let password2Label = password2Input.parentElement.querySelector('label');

        passwordLabel.textContent = "Le password devono coincidere";
        password2Label.textContent = "Le password devono coincidere";

        passwordLabel.classList.add('error');
        passwordInput.classList.add('error');
        passwordInput.value = "";
        password2Input.value = "";
        
        passwordInput.addEventListener('input', function() {
            passwordLabel.textContent = "Password";
            password2Label.textContent = "Conferma Password";
            passwordLabel.classList.remove('error');
            passwordInput.classList.remove('error');
        }, { once: true });
        errore=true;
    }

    if (campiMancanti.length !== 0) {
        for (let mancante of campiMancanti) {
           errore=validazione.validate(mancante,"Campo Obbligatorio");
        }
        errore=true;
    }

    if(errore===true){
        return false;
    }

    return true;
}

window.validateArtisan=validateArtisan;
window.validateCompany=validateCompany;