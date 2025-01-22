<?php

  class Menu {

    private int $menu_id;
    private string $menu_codigo;
    private string $nombre;
    private bool $estado;
    private string $fecha_registro;
    private object $grupo;

    public function __construct() {}

    public function __get(string $propiedad): mixed {
      return (property_exists(get_class($this), $propiedad)) ? ($this->$propiedad) : (null);
    }

    public function __set(string $propiedad, mixed $valor): void {
      if (property_exists(get_class($this), $propiedad)) {
        $this->$propiedad = $valor;
      }
    }

    public function __toString(): string {
      return "Menu {}";
    }
  }
?>
