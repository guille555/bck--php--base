<?php

  class UtilDAO {

    public function retornar_clausulas(array $clausulas): string {
      $resultado = "";
      foreach ($clausulas as $clausula) {
        if (strlen($clausula) > 0) {
          $resultado = $resultado . $clausula . " AND ";
        }
      }
      $resultado = substr($resultado, 0, -5);
      return $resultado;
    }

    public function controlar_tupla_resultado(array $resultado): array {
      return (count($resultado) > 0) ? ($resultado) : (array("cantidad" => 0));
    }
  }
?>
