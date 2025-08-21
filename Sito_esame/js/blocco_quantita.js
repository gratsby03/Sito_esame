"use strict";

//Disabilita gli input di tipo number quando il checkbox non è selezionato
document.addEventListener('DOMContentLoaded', function () {

    const checkboxes=document.querySelectorAll('input[type="checkbox"][name="seleziona[]"]')
    
    for (let i = 0; i < checkboxes.length; i++) {
        const cb = checkboxes[i];
        const id = cb.value;
        const qty = document.getElementById('quantita-' + id);

        qty.disabled = !cb.checked;

        cb.addEventListener('change', function () {
            qty.disabled = !cb.checked;
        });
    }


})