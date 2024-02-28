<?php

namespace AgendaPonto\Models\DTOs;

#[\AllowDynamicProperties] // PERMITE A CRIAÇÃO DINÂMICA DE PROPRIEDADADES
class UsuarioDTO extends ModeloDTO {
  protected $id;
  protected $nome;
  protected $email;
  protected $senha;

  public function getProperties(): array {
    return [
      'id'    => 'id',
      'email' => 'email',
      'nome'  => 'nome',
      'senha' => 'senha'
    ];
  }
}