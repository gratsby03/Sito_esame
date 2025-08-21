"use strict;"

//Verifica che la quantità non sia nulla quando si preme conferma
function validateAcquisto(){
    
    const checkboxes=document.querySelectorAll('input[type="checkbox"][name="seleziona[]"]')
    let errore=true;
    
    for (let i = 0; i < checkboxes.length; i++) {
        const cb = checkboxes[i];
        const id = cb.value;
        const qty = document.getElementById('quantita-' + id);

         qty.classList.remove('error');

        if (cb.checked) {
            const v = qty.value;
            if (v === '') {
                qty.classList.add('error');
                errore = false;
            }
        }

    }

    return errore;
}