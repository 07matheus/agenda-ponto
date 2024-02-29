<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\TarefaDTO;
use AgendaPonto\Models\Model\Tarefa;
use Psr\Http\Message\RequestInterface;
use Slim\Exception\HttpNotFoundException;

class Editar extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function view(): Editar {
    $this->setPathView('paginas/tarefas/cadastro');

    $usuario       = (new Session)->get(['usuario']);
    $requestParams = $this->requestParams;
    $idTarefa      = $requestParams['id'];
    $idUsuario     = $usuario['id'];

    // BUSCA OS DADOS DA TAREFA
    $obTarefa = Tarefa::where([
      ['id', '=', (int) $idTarefa],
      ['id_usuario', '=', (int) $idUsuario]
    ])->first();
    if(!$obTarefa instanceof Tarefa) {
      throw new HttpNotFoundException(
        $this->getSlimRequest(),
        'A tarefa não foi encontrada'
      );
    }

    // TRANSFORMA OS DADOS
    $obTarefaDTO = (new TarefaDTO)->setDados($obTarefa->getAttributes(), false);

    $this->dataOthers = array_merge([
      'isAtualizacao'       => true,
      'tituloFormulario'    => 'Editar tarefa',
      'tituloPagina'        => 'Edição',
      'nomeBotaoFormulario' => 'Salvar'
    ], $obTarefaDTO->toArray(true, true));

    // SELETORES
    $this->dataOthers['checkedPrioridadeBaixa'] = ($obTarefaDTO->prioridade == 'baixa') ? 'checked': '';
    $this->dataOthers['checkedPrioridadeMedia'] = ($obTarefaDTO->prioridade == 'media') ? 'checked': '';
    $this->dataOthers['checkedPrioridadeAlta']  = ($obTarefaDTO->prioridade == 'alta') ? 'checked': '';
    $this->dataOthers['checkedConcluida']       = ($obTarefaDTO->concluida == 's') ? 'checked': '';
    $this->dataOthers['checkedNaoConcluida']    = ($obTarefaDTO->concluida != 's') ? 'checked': '';

    return $this;
  }
}