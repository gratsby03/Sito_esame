"use strict";

function validateLogin(username, password){
    const regUsername = /^[A-Za-z][A-Za-z0-9_-]{3,9}$/;
    const regPassword = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.;+=])[A-Za-z\d!"#$%&'()*+,\-./:;<=>?@[\\\]^_{|}~]{8,16}$/;

    let campiMancanti = [];
    let passwordInput = document.querySelector('input[name="password"]');
    let passwordLabel = passwordInput.parentElement.querySelector('label');
    let usernameInput = document.querySelector('input[name="username"]');
    let usernameLabel = usernameInput.parentElement.querySelector('label');
    let errore = false;

    if (username === "") campiMancanti.push("username");
    else if (!regUsername.test(username)) {
        usernameLabel.textContent = "Username non valido";
        
        usernameLabel.classList.add('error');
        usernameInput.classList.add('error');
        usernameInput.value = "";
        passwordInput.value = "";
        
        usernameInput.addEventListener('input', function() {
            usernameLabel.textContent = "Username";
            usernameLabel.classList.remove('error');
            usernameInput.classList.remove('error');
        }, { once: true });
        errore=true;
    }

    if (password === "") campiMancanti.push("password");
    else if (!regPassword.test(password)) {
        passwordLabel.textContent = "Password non valida";
        
        passwordLabel.classList.add('error');
        passwordInput.classList.add('error');
        usernameInput.value = "";
        passwordInput.value = "";
        
        passwordInput.addEventListener('input', function() {
            passwordLabel.textContent = "Password";
            passwordLabel.classList.remove('error');
            passwordInput.classList.remove('error');
        }, { once: true });
        errore=true;
    }
    
    if(errore===true){
        return false;
    }

    if (campiMancanti.length!==0) {
        for (const mancante of campiMancanti) {
            const generalInput= document.querySelector(`input[name="${mancante}"]`);
            const generalLabel = generalInput.parentElement.querySelector('label');
            generalLabel.textContent = "Campo obbligatorio";
            generalLabel.classList.add('error');
            generalInput.addEventListener('input', function() {
            usernameLabel.textContent = "Username";
            passwordLabel.textContent = "Password";
            generalLabel.classList.remove('error');
            generalInput.classList.remove('error');
            }, { once: true });
        }
        return false;
    }

    return true;
}