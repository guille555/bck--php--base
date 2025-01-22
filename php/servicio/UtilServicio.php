<?php

  class UtilServicio {
    
    public function controlar_tupla(array $tupla): void {
      (count($tupla) === 0) && (throw new Exception("no existe el registro"));
    }

    public function retornar_objeto_comun(): object {
      $objeto = new stdClass();
      $objeto->id = 0;
      $objeto->estado = false;
      return $objeto;
    }

    public function retornar_ventana(int $pagina, int $limite): int {
      $resultado = $limite * ($pagina - 1);
      return $resultado;
    }

    public function retornar_cantidad_paginas(int $total_tuplas, int $limite): int {
      $cantidad = $total_tuplas / $limite;
      $cantidad = number_format($cantidad, 2);
      $cantidad = floatval($cantidad);
      $cantidad = ceil($cantidad);
      $cantidad = intval($cantidad);
      return $cantidad;
    }

    private function filtrar_parametros(array $parametros): array {
      $resultado = array();
      foreach ($parametros as $clave => $valor) {
        $clave = str_replace(":", "", $clave);
        $valor = str_replace("%", "", $valor);
        $resultado[$clave] = $valor;
      }
      return $resultado;
    }

    private function retornar_pagina_anterior(string $ruta, int $pagina, array $parametros): string {
      $claves = $this->filtrar_parametros($parametros);
      $claves["pagina"] = $pagina - 1;
      $parametros_consulta = http_build_query($claves);
      $parametros_consulta = strtolower($parametros_consulta);
      $resultado = $ruta . $parametros_consulta;
      return (($pagina - 1) === 0) ? ("") : ($resultado);
    }

    private function retornar_pagina_posterior(string $ruta, int $pagina, int $limite, int $cantidad_tuplas, array $parametros): string {
      $claves = $this->filtrar_parametros($parametros);
      $claves["pagina"] = $pagina + 1;
      $parametros_consulta = http_build_query($claves);
      $parametros_consulta = strtolower($parametros_consulta);
      $resultado = $ruta . $parametros_consulta;
      return (($pagina * $limite) < $cantidad_tuplas) ? ($resultado) : ("");
    }

    public function retornar_lista(string $ruta, int $pagina, int $limite, int $cantidad_tuplas, array $parametros): array {
      $respuesta = array();
      $respuesta["pagina_actual"] = $pagina;
      $respuesta["pagina_anterior"] = $this->retornar_pagina_anterior($ruta, $pagina, $parametros);
      $respuesta["pagina_posterior"] = $this->retornar_pagina_posterior($ruta, $pagina, $limite, $cantidad_tuplas, $parametros);
      $respuesta["cantidad_paginas"] = $this->retornar_cantidad_paginas($cantidad_tuplas, $limite);
      $respuesta["cantidad_elementos"] = $cantidad_tuplas;
      return $respuesta;
    }
  }
?>
