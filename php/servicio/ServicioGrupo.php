<?php
  require_once __DIR__ . "/../modelo/Grupo.php";
  require_once __DIR__ . "/../dao/GrupoDAO.php";
  require_once __DIR__ . "/ServicioMenu.php";
  require_once __DIR__ . "/../util/EstadoSistema.php";
  require_once __DIR__ . "/UtilServicio.php";

  date_default_timezone_set("America/Argentina/Buenos_Aires");

  class ServicioGrupo {

    private function convertir_tupla_objeto(array $tupla): object {
      $fn_listar_menus = function(int $id) {
        $servicio = new ServicioMenu();
        $lista = $servicio->listar_grupo($id);
        return $lista;
      };
      $objeto = new stdClass();
      $objeto->id = intval($tupla["grupo_id"]);
      $objeto->codigo = strval($tupla["grupo_codigo"]);
      $objeto->nombre = strval($tupla["nombre"]);
      $objeto->estado = boolval($tupla["estado"]);
      $objeto->fecha_registro = strval($tupla["fecha_registro"]);
      $objeto->lista_menus = $fn_listar_menus(intval($tupla["grupo_id"]));
      return $objeto;
    }

    private function convertir_tuplas_lista(array $tuplas): array {
      $lista = array();
      foreach ($tuplas as $item) {
        $objeto = $this->convertir_tupla_objeto($item);
        array_push($lista, $objeto);
      }
      return $lista;
    }

    private function obtener_grupo_cargado(array $datos): Grupo {
      $grupo = new Grupo();
      $grupo->__set("nombre", strtoupper(strval($datos["nombre"])));
      return $grupo;
    }

    public function guardar(array $datos): int {
      $fn_retornar_codigo = function(int $id): string {
        $codigo = "DBR00" . strval($id) . "00GPR";
        return $codigo;
      };
      try {
        $dao = new GrupoDAO();
        $grupo = $this->obtener_grupo_cargado($datos);
        $id = $dao->guardar($grupo);
        $codigo = $fn_retornar_codigo($id);
        $dao->agregar_codigo($id, $codigo);
        return EstadoSistema::OPERACION_EXITOSA->value;
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function actualizar(array $datos): int {
      try {
        $dao = new GrupoDAO();
        $grupo = $this->buscar_codigo(strval($datos["codigo"]));
        if (($grupo->id > 0) && ($grupo->estado)) {
          $grupo_actualizar = $this->obtener_grupo_cargado($datos);
          $grupo_actualizar->__set("grupo_id", $grupo->id);
          $dao->actualizar($grupo_actualizar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception("no existe la tupla", 11);
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function conmutar(array $datos): int {
      try {
        $dao = new GrupoDAO();
        $grupo = $this->buscar_codigo(strval($datos["codigo"]));
        if ($grupo->id > 0) {
          $grupo_conmutar = new Grupo();
          $grupo_conmutar->__set("grupo_id", $grupo->id);
          $grupo_conmutar->__set("estado", !$grupo->estado);
          $dao->conmutar($grupo_conmutar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception("no existe la tupla", 11);
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function eliminar(array $datos): int {
      try {
        $dao = new GrupoDAO();
        $grupo = $this->buscar_codigo(strval($datos["codigo"]));
        if ($grupo->id > 0) {
          $grupo_eliminar = new Grupo();
          $grupo_eliminar->__set("grupo_id", $grupo->id);
          $dao->eliminar($grupo_eliminar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception("no existe la tupla", 11);
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function buscar_id(int $id): object {
      $util = new UtilServicio();
      try {
        $dao = new GrupoDAO();
        $tupla = $dao->buscar_id($id);
        $util->controlar_tupla($tupla);
        $grupo = $this->convertir_tupla_objeto($tupla);
        return $grupo;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    public function buscar_codigo(string $codigo): object {
      $util = new UtilServicio();
      try {
        $dao = new GrupoDAO();
        $tupla = $dao->buscar_codigo($codigo);
        $util->controlar_tupla($tupla);
        $grupo = $this->convertir_tupla_objeto($tupla);
        return $grupo;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    private function retornar_parametros_consulta(bool $estado, string $nombre, string $codigo): array {
      $fn_retornar_parametro_estado = function(bool $estado): array {
        return array(":estado" => boolval($estado));
      };
      $fn_retornar_parametro_nombre = function(string $nombre): array {
        $control = (isset($nombre) && is_string($nombre) && (strlen($nombre) > 0));
        return ($control) ? (array(":nombre" => strtoupper($nombre) . "%")) : (array());
      };
      $fn_retornar_parametro_codigo = function(string $codigo): array {
        $control = (isset($codigo) && is_string($codigo) && (strlen($codigo) > 0));
        return ($control) ? (array(":codigo" => strtoupper($codigo))) : (array());
      };
      $parametros = array_merge(
        $fn_retornar_parametro_estado($estado),
        $fn_retornar_parametro_nombre($nombre),
        $fn_retornar_parametro_codigo($codigo),
      );
      return $parametros;
    }

    private function filtrar_lista_retorno(array $lista): array {
      foreach ($lista as $item) {
        unset($item->id);
      }
      return $lista;
    }

    public function buscar_parametros(bool $estado, string $nombre, string $codigo, int $pagina): array {
      $util = new UtilServicio();
      $dao = new GrupoDAO();
      $parametros = $this->retornar_parametros_consulta($estado, $nombre, $codigo);
      $cantidad_tuplas = $dao->contar_tuplas($parametros);
      $ventana = $util->retornar_ventana($pagina, 128);
      $lista = $dao->buscar_parametros($ventana, 128, $parametros);
      $lista = $this->convertir_tuplas_lista($lista);
      $lista = $this->filtrar_lista_retorno($lista);
      $resultado = $util->retornar_lista("/ruta/lista.php?", $pagina, 128, $cantidad_tuplas, $parametros);
      $resultado["elementos"] = $lista;
      return $resultado;
    }
  }
?>
