<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\TarefaDTO;
use AgendaPonto\Models\Model\Tarefa;
use DateTime;
use Psr\Http\Message\RequestInterface;
use Slim\Exception\HttpNotFoundException;

class Editar extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    $this->status     = true;
    $quantidadeMinima = null;
    $quantidadeMaxima = null;

    switch($campo) {
      case 'nome':
        $quantidadeMinima = 1;
        $quantidadeMaxima = 50;
      break;

      case 'prioridade':
        $opcoes = ['baixa', 'media', 'alta'];
        if(!in_array($valorCampo, $opcoes)) {
          $this->response = "As opções permitidas para o campo '$campo', são: " . implode(', ', $opcoes);
          $this->status   = false;
        }
      
      return;
      
      case 'dataVencimento':
        if(!(strlen($valorCampo) > 1 && strlen($valorCampo) <= 11)) {
          $this->response = 'O campo de data de vencimento não foi informado';
          $this->status   = false;
        }

      return;
    }

    if(is_numeric($quantidadeMinima) && is_numeric($quantidadeMaxima)) {
      $quantidade = strlen($valorCampo);
      if($quantidade < $quantidadeMinima || $quantidade > $quantidadeMaxima) {
        $this->response = "O campo '{$campo}' possui limitação de caracteres. Min.({$quantidadeMinima}) e Max.({$quantidadeMaxima})";
        $this->status = false;
      }
    }
  }

  private function setDadosExibicaoLayout(TarefaDTO $obTarefaDTO): void {
    $this->setPathView('paginas/tarefas/cadastro');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('css', 'editar-tarefa');
    $this->getResourcesFilesCompiled('js', 'editar-tarefa');
    $idTarefa = $obTarefaDTO->id;
    
    $this->dataOthers = array_merge([
      'isAtualizacao'       => true,
      'tituloFormulario'    => 'Editar tarefa',
      'tituloPagina'        => 'Edição',
      'nomeBotaoFormulario' => 'Salvar',
      'tipoFormulario'      => 'ver/' . $idTarefa
    ], $obTarefaDTO->toArray(true, true));

    // SELETORES
    $prioridadeBaixa = ($obTarefaDTO->prioridade == 'baixa');
    $prioridadeMedia = ($obTarefaDTO->prioridade == 'media');
    $prioridadeAlta  = ($obTarefaDTO->prioridade == 'alta');
    $this->dataOthers['checkedPrioridadeBaixa'] = $prioridadeBaixa ? 'checked': '';
    $this->dataOthers['checkedPrioridadeMedia'] = $prioridadeMedia ? 'checked': '';
    $this->dataOthers['checkedPrioridadeAlta']  = $prioridadeAlta ? 'checked': '';

    $concluida = ($obTarefaDTO->concluida == 's');
    $this->dataOthers['checkedConcluida']    = $concluida ? 'checked': '';
    $this->dataOthers['checkedNaoConcluida'] = !$concluida ? 'checked': '';
  }

  public function view(): Editar {
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

    $this->setDadosExibicaoLayout($obTarefaDTO);    

    return $this;
  }

  public function update($obTarefaDTO): Editar {
    // VERIFICA SE A TAREFA ATUAL PERTENCE AO USUÁRIO LOGADO
    $usuario       = (new Session)->get(['usuario']);
    $obTarefaAntes = Tarefa::where([
      ['id', '=', $obTarefaDTO->id],
      ['id_usuario', '=', ($usuario['id'] ?? 0)]
    ])->first();
    if(!$obTarefaAntes instanceof Tarefa) {
      throw new HttpNotFoundException(
        $this->getSlimRequest(),
        'A tarefa não foi encontrada'
      );
    }

    // VALIDA OS CAMPOS OBRIGATÓRIOS
    $this->validarDadosFormulario($obTarefaDTO);

    if($this->status) {
      // SALVA AS INFORAÇÕES QUE FORAM ALTERADAS
      if(is_null($obTarefaDTO->nome)) $obTarefaDTO->set('nome', $obTarefaAntes->nome);
      if(is_null($obTarefaDTO->prioridade)) $obTarefaDTO->set('prioridade', $obTarefaAntes->prioridade);
      if(is_null($obTarefaDTO->descricao)) $obTarefaDTO->set('descricao', $obTarefaAntes->descricao);
      if(is_null($obTarefaDTO->dataVencimento)) $obTarefaDTO->set('dataVencimento', $obTarefaAntes->data_vencimento);
      if(is_null($obTarefaDTO->concluida)) $obTarefaDTO->set('concluida', $obTarefaAntes->concluida);
      
      $dadosAtualizar = [
        'nome'            => $obTarefaDTO->nome,
        'prioridade'      => $obTarefaDTO->prioridade,
        'descricao'       => $obTarefaDTO->descricao,
        'data_vencimento' => $obTarefaDTO->dataVencimento,
        'concluida'       => $obTarefaDTO->concluida
      ];

      // SALVA A EDIÇÃO DA TAREFA
      $sucesso        = (Tarefa::where('id', $obTarefaAntes->id)->update($dadosAtualizar) > 0);
      $this->status   = $sucesso;
      $this->response = $sucesso ? 'Tarefa atualizada com sucesso!': 'Não foi possível atualizar a terefa. Tente novamente mais tarde.';
    }

    // EXIBIÇÃO DO LAYOUT
    $this->setDadosExibicaoLayout($obTarefaDTO);

    return $this;
  }
}