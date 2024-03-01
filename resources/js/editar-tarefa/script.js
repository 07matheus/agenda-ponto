document.querySelectorAll('.option input[type="radio"]').forEach((item, _) => {
  addEvent(item);

  item.addEventListener('change', alterInputChecked);
});

function alterInputChecked() {
  let valorIgnorar   = this.value;
  let nomeNaoIgnorar = this.getAttribute('name');
  this.parentElement.classList.add('checked');

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
  } else {
    item.removeAttribute('checked');
    item.parentNode.classList.remove('checked');
  }
}