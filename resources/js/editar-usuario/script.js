document.querySelectorAll('.option input[type="radio"]').forEach((item, _) => {
  addEvent(item);

  item.addEventListener('change', alterInputChecked);
});

function alterInputChecked() {
  let valorIgnorar   = this.value;
  let nomeNaoIgnorar = this.getAttribute('name');
  this.parentElement.classList.add('checked');

  mostrarBoxSenha(this);
  
  document.querySelectorAll('.option input[type="radio"]').forEach((item, _) => {
    if(item.value !== valorIgnorar && item.getAttribute('name') === nomeNaoIgnorar) {
      item.removeAttribute('checked');
      item.parentElement.classList.remove('checked');
    }
  });
}

function addEvent(item) {
  if(item.getAttribute('checked') !== null) {
    item.setAttribute('checked', '');
    item.parentNode.classList.add('checked');
    mostrarBoxSenha(item);
  } else {
    item.removeAttribute('checked');
    item.parentNode.classList.remove('checked');
  }
}

function mostrarBoxSenha(item) {
  let elemento = document.querySelector('[data-mostrar-senha]');

  console.log(item.value);

  if(item.value == 's') {
    elemento.classList.remove('hide');
  } else {
    elemento.classList.add('hide');
  }
}