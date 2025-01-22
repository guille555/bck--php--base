<?php

  class Grupo {

    private int $grupo_id;
    private string $grupo_codigo;
    private string $nombre;
    private bool $estado;
    private string $fecha_registro;
    private array $lista_menus;

    public function __construct() {
      $this->lista_menus = array();
    }

    public function __get(string $propiedad): mixed {
      return (property_exists(get_class($this), $propiedad)) ? ($this->$propiedad) : (null);
    }

    public function __set(string $propiedad, mixed $valor): void {
      if (property_exists(get_class($this), $propiedad)) {
        $this->$propiedad = $valor;
      }
    }

    public function __toString(): string {
      return "Grupo {}";
    }
  }
?>
