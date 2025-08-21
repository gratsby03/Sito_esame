"use strict";

//Script per la gestione della selezione azienda/artigiano all'interno del form di registrazione

document.addEventListener('DOMContentLoaded', function() {

    const tipoAzienda = document.getElementById('tipo-azienda');
    const tipoArtigiano = document.getElementById('tipo-artigiano');
    const formAzienda = document.querySelector('.registra-azienda');
    const formArtigiano = document.querySelector('.registra-artigiano');

    function toggleForms() {
        if (tipoAzienda.checked) {
            formAzienda.style.display = 'block';
            formArtigiano.style.display = 'none';
        } else if (tipoArtigiano.checked) {
            formAzienda.style.display = 'none';
            formArtigiano.style.display = 'block';
        }
    }

    toggleForms();

    tipoAzienda.addEventListener('change', function() {
        toggleForms();
    });
    
    tipoArtigiano.addEventListener('change', function() {
        toggleForms();
    });

});