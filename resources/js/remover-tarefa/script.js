document.querySelectorAll('[name="remover"]').forEach((item, _) => {
  item.addEventListener('click', removerTarefa); 
});

function removerTarefa() {
  let idTarefa = parseInt(document.querySelector('[name="id"]').value);
  console.log(idTarefa);

  if(this.value == 's') {
    document.getElementById('removerTarefa').submit();
  } else {
    window.location = '/dashboard';
  }
}