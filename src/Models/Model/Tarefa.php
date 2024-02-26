<?php

namespace AgendaPonto\Models\Model;

use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model {
  protected $fillable = ['nome', 'id_usuario', 'prioridade', 'descricao', 'data_criacao', 'concluida'];

  public $timestamps = false;
}