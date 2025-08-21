'use strict';

export function validate(nomeCampo, messaggio){
    var genericInput = document.querySelector(`input[name="${nomeCampo}"]`);
    var genericLabel = genericInput.parentElement.querySelector(`label[for="${genericInput.id}"]`);

    genericLabel.textContent = messaggio;
    console.log(genericInput.id);
    
    genericLabel.classList.add('error');
    genericInput.classList.add('error');
    genericInput.value = "";
    
    genericInput.addEventListener('input', function() {
        genericLabel.textContent = nomeCampo;
        genericLabel.classList.remove('error');
        genericInput.classList.remove('error');
    }, { once: true });
    return true;
}

export function validateEuro(euro){
    const numero = parseFloat(euro);
    if (isNaN(numero)) { 
        return false; 
    }
    const cents = Math.round(numero * 100);
    return (cents % 5 === 0);
}

export function validateDate(data){
    const[aaaa,mm,gg]=data.split("-");
    const anno = parseInt(aaaa, 10);
    const mese = parseInt(mm, 10) - 1;
    const giorno = parseInt(gg, 10);

    const prova = new Date(anno, mese, giorno);

    return (prova.getFullYear() === anno && prova.getMonth() === mese && prova.getDate() === giorno);
}