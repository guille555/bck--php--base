<?php

  class UsuarioEmpresa {

    private int $usuario_empresa_id;
    private string $usuario_empresa_codigo;
    private string $nombre_usuario;
    private string $clave;
    private bool $estado;
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
      return "UsuarioEmpresa {}";
    }
  }
?>
