<?php

namespace AgendaPonto\Models\DTOs;

abstract class ModeloDTO {
  /**
   * Recupera o mapeamento das propriedades: [propriedadeClasse => propriedadeBancoDeDados]
   * @return array
   */
  abstract public function getProperties(): array;

  /**
   * Grava os dados na classe instanciada
   * @param  array      $dados      Dados que serão inseridos
   * @return ModeloDTO
   */
  public function setDados(array $dados = []): ModeloDTO {
    foreach($dados as $property => $value) $this->set($property, $value);

    return $this;
  }

  /**
   * Grava os dados na propriedade
   * @param  string       $property       Propriedade do DTO
   * @param  mixed        $value          Valor da propriedade
   * @return ModeloDTO
   */
  public function set(string $property, mixed $value): ModeloDTO {
    if(in_array($property, array_keys($this->getProperties()))) $this->{$property} = $value;

    return $this;
  }

  /**
   * Recupera os dados de uma propriedade
   * @param  string       $property       Propriedade
   * @return mixed
   */
  public function __get(string $property): mixed {
    if(in_array($property, array_keys(get_class_vars($this::class)))) return $this->{$property};

    return null;
  }

  /**
   * Transforma os dados do DTO em array
   * @param  bool      $forDatabase      Define se os campos da array serão com base na propriedade do banco ou da classe
   * @param  bool      $nullValues       Define se irá retornar os valores nulos
   * @return array
   */
  public function toArray(bool $forDatabase = true, bool $nullValues = false): array {
    $data            = [];
    $properties      = $this->getProperties();
    $propertiesClass = array_keys($properties);
    $propertiesDB    = array_keys($properties);
    $properties      = $forDatabase ? $propertiesDB: $propertiesClass;
    
    foreach($propertiesClass as $key => $property) {
      $value = $this->{$property};
      if(is_null($value) && !$nullValues) continue;

      $data[$properties[$key]] = $value;
    }

    return $data;
  }
}