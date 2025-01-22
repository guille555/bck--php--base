<?php
  require_once __DIR__ . "/../modelo/Menu.php";
  require_once __DIR__ . "/../dao/MenuDAO.php";
  require_once __DIR__ . "/ServicioGrupo.php";
  require_once __DIR__ . "/../util/EstadoSistema.php";
  require_once __DIR__ . "/UtilServicio.php";

  date_default_timezone_set("America/Argentina/Buenos_Aires");

  class ServicioMenu {

    private function buscar_grupo_codigo(string $codigo): object {
      $servicio = new ServicioGrupo();
      $grupo = $servicio->buscar_codigo($codigo);
      return $grupo;
    }

    private function convertir_tupla_objeto(array $tupla): object {
      $objeto = new stdClass();
      $objeto->id = intval($tupla["menu_id"]);
      $objeto->codigo = strval($tupla["menu_codigo"]);
      $objeto->nombre = strval($tupla["nombre"]);
      $objeto->estado = boolval($tupla["estado"]);
      $objeto->fecha_registro = strval($tupla["fecha_registro"]);
      return $objeto;
    }

    private function convertir_tuplas_lista(array $tuplas): array {
      $lista = array();
      foreach ($tuplas as $tupla) {
        $objeto = $this->convertir_tupla_objeto($tupla);
        array_push($lista, $objeto);
      }
      return $lista;
    }

    private function obtener_menu_cargado(array $datos): Menu {
      $menu = new Menu();
      $menu->__set("nombre", strtoupper(strval($datos["nombre"])));
      return $menu;
    }

    private function preparar_menu(array $datos): Menu {
      $grupo = $this->buscar_grupo_codigo(strval($datos["grupo"]["codigo"]));
      $menu = $this->obtener_menu_cargado($datos);
      $menu->__set("grupo", $grupo);
      return $menu;
    }

    public function guardar(array $datos): int {
      $fn_retornar_codigo = function(int $id): string {
        $codigo = "DBR00" . strval($id) . "00MNU";
        return $codigo;
      };
      try {
        $dao = new MenuDAO();
        $menu = $this->preparar_menu($datos);
        $id = $dao->guardar($menu);
        $codigo = $fn_retornar_codigo($id);
        $dao->agregar_codigo($id, $codigo);
        return EstadoSistema::OPERACION_EXITOSA->value;
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function actualizar(array $datos): int {
      try {
        $dao = new MenuDAO();
        $menu = $this->buscar_codigo(strval($datos["codigo"]));
        if (($menu->id > 0) && ($menu->estado)) {
          $menu_actualizar = $this->preparar_menu($datos);
          $menu_actualizar->__set("menu_id", $menu->id);
          $dao->actualizar($menu_actualizar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception();
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function conmutar(array $datos): int {
      try {
        $dao = new MenuDAO();
        $menu = $this->buscar_codigo(strval($datos["codigo"]));
        if ($menu->id > 0) {
          $menu_conmutar = new Menu();
          $menu_conmutar->__set("menu_id", $menu->id);
          $menu_conmutar->__set("estado", !$menu->estado);
          $dao->conmutar($menu_conmutar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception();
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function eliminar(array $datos): int {
      try {
        $dao = new MenuDAO();
        $menu = $this->buscar_codigo(strval($datos["codigo"]));
        if ($menu->id > 0) {
          $menu_eliminar = new Menu();
          $menu_eliminar->__set("menu_id", $menu->id);
          $dao->eliminar($menu_eliminar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        }
        throw new Exception();
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function buscar_id(int $id): object {
      $util = new UtilServicio();
      try {
        $dao = new MenuDAO();
        $tupla = $dao->buscar_id($id);
        $util->controlar_tupla($tupla);
        $menu = $this->convertir_tupla_objeto($tupla);
        return $menu;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    public function buscar_codigo(string $codigo): object {
      $util = new UtilServicio();
      try {
        $dao = new MenuDAO();
        $tupla = $dao->buscar_codigo($codigo);
        $util->controlar_tupla($tupla);
        $menu = $this->convertir_tupla_objeto($tupla);
        return $menu;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    public function listar_grupo(int $grupo_id): array {
      try {
        $dao = new MenuDAO();
        $tuplas = $dao->listar_grupo($grupo_id);
        $lista = $this->convertir_tuplas_lista($tuplas);
        $lista = $this->filtrar_lista_retorno($lista);
        return $lista;
      } catch (Exception $exc) {
        return array();
      }
    }

    private function retornar_parametros_consulta(bool $estado, string $nombre, string $codigo, string $grupo_codigo): array {
      $fn_retornar_parametro_estado = function(bool $estado): array {
        return array(":estado" => boolval($estado));
      };
      $fn_retornar_parametro_nombre = function(string $nombre): array {
        $control = (isset($nombre) && is_string($nombre) && (strlen($nombre) > 0));
        return ($control) ? (array(":nombre" => (strtoupper($nombre) . "%"))) : (array());
      };
      $fn_retornar_parametro_codigo = function(string $codigo): array {
        $control = (isset($codigo) && is_string($codigo) && (strlen($codigo) > 0));
        return ($control) ? (array(":codigo" => strtoupper($codigo))) : (array());
      };
      $fn_retornar_parametro_grupo = function(string $grupo_codigo): array {
        $control = (isset($grupo_codigo) && is_string($grupo_codigo) && (strlen($grupo_codigo) > 0));
        return ($control) ? (array(":grupo" => $grupo_codigo)) : (array());
      };
      $parametros = array_merge(
        $fn_retornar_parametro_estado($estado),
        $fn_retornar_parametro_nombre($nombre),
        $fn_retornar_parametro_codigo($codigo),
        $fn_retornar_parametro_grupo($grupo_codigo)
      );
      return $parametros;
    }

    private function filtrar_lista_retorno(array $lista): array {
      foreach ($lista as $item) {
        unset($item->id);
      }
      return $lista;
    }

    private function cambiar_parametro_grupo(string $grupo_codigo, array $parametros): array {
      if (isset($grupo_codigo) && is_string($grupo_codigo) && (strlen($grupo_codigo) > 0)) {
        $grupo = $this->buscar_grupo_codigo($grupo_codigo);
        $parametros[":grupo"] = $grupo->id;
      }
      return $parametros;
    }

    public function buscar_parametros(bool $estado, string $nombre, string $codigo, string $grupo_codigo, int $pagina): array {
      $util = new UtilServicio();
      $dao = new MenuDAO();
      $parametros = $this->retornar_parametros_consulta($estado, $nombre, $codigo, $grupo_codigo);
      $parametros_consulta = $this->cambiar_parametro_grupo($grupo_codigo, $parametros);
      $cantidad_tuplas = $dao->contar_tuplas($parametros_consulta);
      $ventana = $util->retornar_ventana($pagina, 32);
      $lista = $dao->buscar_parametros($ventana, 32, $parametros_consulta);
      $lista = $this->convertir_tuplas_lista($lista);
      $lista = $this->filtrar_lista_retorno($lista);
      $resultado = $util->retornar_lista("/ruta/lista.php?", $pagina, 32, $cantidad_tuplas, $parametros);
      $resultado["elementos"] = $lista;
      return $resultado;
    }
  }
?>
