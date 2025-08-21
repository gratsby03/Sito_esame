"use strict";

  function chiudiOverlay(overlay,content) {
    overlay.classList.remove('attivo');
    content.classList.remove('overlay');
  }

    function apriOverlay(overlay,content) {
    overlay.classList.add('attivo');
    content.classList.add('overlay');
  }



document.addEventListener('DOMContentLoaded', () => {
  const overlay   = document.getElementById('overlay-aggiunta');
  const btn = document.getElementById('bottone-aggiunta');
  const btnChiudi = document.getElementById('bottone-chiusura1');
  const content = document.getElementById('overlay-offerta');


  btn.addEventListener('click', () => apriOverlay(overlay,content));
  btnChiudi.addEventListener('click', () => chiudiOverlay(overlay,content));  
});

document.addEventListener('DOMContentLoaded', () => {
  const overlay   = document.getElementById('overlay-modifica');
  const btnChiudi = document.getElementById('bottone-chiusura2');
  const content = document.getElementById('overlay-offerta');
  
  const inputId = document.getElementById('id');
  const inputNome = document.getElementById('name-mod');
  const inputDescrizione = document.getElementById('descrizione-mod');
  const inputData = document.getElementById('date-mod');
  const inputQuantita = document.getElementById('quantity-mod');
  const inputCosto = document.getElementById('cost-mod');

  const bottoni=document.querySelectorAll('.btn-modifica');
  bottoni.forEach(bottone => {
    bottone.addEventListener('click', () => {
    const {id, nome, descrizione, data, quantita, costo } = bottone.dataset;

    inputId.value = id;
    inputNome.value = nome;
    inputDescrizione.value = descrizione;
    inputData.value = data;
    inputQuantita.value = quantita;
    inputCosto.value = costo;

    apriOverlay(overlay,content);
    });
  });

  btnChiudi.addEventListener('click', () => chiudiOverlay(overlay,content));  
});