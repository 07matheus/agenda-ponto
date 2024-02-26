<?php

namespace AgendaPonto\Models\DTOs;

class TarefaDTO extends ModeloDTO {
  protected $id;
  protected $idUsuario;
  protected $nome;
  protected $prioridade;
  protected $descricao;
  protected $dataCriacao;
  protected $concluida;

  public function getProperties(): array {
    return [
      'id'          => 'id',
      'idUsuario'   => 'id_usuario',
      'nome'        => 'nome',
      'prioridade'  => 'prioridade',
      'descricao'   => 'descricao',
      'dataCriacao' => 'data_criacao',
      'concluida'   => 'concluida'
    ];
  }
}