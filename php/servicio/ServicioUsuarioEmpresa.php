<?php
  require_once __DIR__ . "/../modelo/UsuarioEmprersa.php";
  require_once __DIR__ . "/../dao/UsuarioEmpresaDAO.php";
  require_once __DIR__ . "/../servicio/ServicioGrupo.php";
  require_once __DIR__ . "/../util/EstadoSistema.php";
  require_once __DIR__ . "/UtilServicio.php";

  date_default_timezone_set("America/Argentina/Buenos_Aires");

  class ServicioUsuarioEmpresa {

    private function buscar_grupo_codigo(string $codigo): object {
      $servicio = new ServicioGrupo();
      $grupo = $servicio->buscar_codigo($codigo);
      return $grupo;
    }

    private function convertir_tupla_objeto(array $tupla): object {
      $objeto = new stdClass();
      $objeto->id = intval($tupla["usuario_empresa_id"]);
      $objeto->codigo = strval($tupla["usuario_empresa_codigo"]);
      $objeto->nombre_usuario = strval($tupla["nombre_usuario"]);
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
      return $lista;;
    }

    private function obtener_usuario_empresa_cargado(array $datos): UsuarioEmpresa {
      $usuario_empresa = new UsuarioEmpresa();
      $usuario_empresa->__set("nombre_usuario", strval($datos["nombreusuario"]));
      $usuario_empresa->__set("clave", strval($datos["clave"]));
      return $usuario_empresa;
    }

    private function preparar_usuario_empresa(array $datos): UsuarioEmpresa {
      $grupo = $this->buscar_grupo_codigo(strval($datos["grupo"]["codigo"]));
      $usuario_empresa = $this->obtener_usuario_empresa_cargado($datos);
      $usuario_empresa->__set("grupo", $grupo);
      return $usuario_empresa;
    }

    public function guardar(array $datos): int {
      $fn_retornar_codigo = function(int $id) {
        $codigo = "DBR00" . strval($id) . "00UEA";
        return $codigo;
      };
      try {
        $dao = new UsuarioEmpresaDAO();
        $usuario_empresa = $this->preparar_usuario_empresa($datos);
        $id = $dao->guardar($usuario_empresa);
        $codigo = $fn_retornar_codigo($id);
        $dao->agregar_codigo($id, $codigo);
        return EstadoSistema::OPERACION_EXITOSA->value;
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function actualizar($datos): int {
      try {
        $dao = new UsuarioEmpresaDAO();
        $usuario_empresa = $this->buscar_codigo(strval($datos["codigo"]));
        if (($usuario_empresa->id > 0) && ($usuario_empresa->estado)) {
          $usuario_empresa_actualizar = $this->preparar_usuario_empresa($datos);
          $usuario_empresa_actualizar->__set("usuario_empresa_id", $usuario_empresa->id);
          $dao->actualizar($usuario_empresa_actualizar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        };
        throw new Exception("no existe el objeto");
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function conmutar(array $datos): int {
      try {
        $dao = new UsuarioEmpresaDAO();
        $usuario_empresa = $this->buscar_codigo(strval($datos["codigo"]));
        if ($usuario_empresa->id > 0) {
          $usuario_empresa_conmutar = new UsuarioEmpresa();
          $usuario_empresa_conmutar->__set("usuario_empresa_id", $usuario_empresa->id);
          $usuario_empresa_conmutar->__set("estado", !$usuario_empresa->estado);
          $dao->conmutar($usuario_empresa_conmutar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        };
        throw new Exception("no existe el objeto");
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function eliminar(array $datos): int {
      try {
        $dao = new UsuarioEmpresaDAO();
        $usuario_empresa = $this->buscar_codigo(strval($datos["codigo"]));
        if ($usuario_empresa->id > 0) {
          $usuario_empresa_eliminar = new UsuarioEmpresa();
          $usuario_empresa_eliminar->__set("usuario_empresa_id", $usuario_empresa->id);
          $dao->eliminar($usuario_empresa_eliminar);
          return EstadoSistema::OPERACION_EXITOSA->value;
        };
        throw new Exception("no existe el objeto");
      } catch (Exception $exc) {
        return EstadoSistema::OPERACION_ERRONEA->value;
      }
    }

    public function buscar_id(int $id): object {
      $util = new UtilServicio();
      try {
        $dao = new UsuarioEmpresaDAO();
        $tupla = $dao->buscar_id($id);
        $util->controlar_tupla($tupla);
        $usuario_empresa = $this->convertir_tupla_objeto($tupla);
        return $usuario_empresa;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    public function buscar_codigo(string $codigo): object {
      $util = new UtilServicio();
      try {
        $dao = new UsuarioEmpresaDAO();
        $tupla = $dao->buscar_codigo($codigo);
        $util->controlar_tupla($tupla);
        $usuario_empresa = $this->convertir_tupla_objeto($tupla);
        return $usuario_empresa;
      } catch (Exception $exc) {
        $objeto = $util->retornar_objeto_comun();
        return $objeto;
      }
    }

    private function retornar_parametros_consulta(bool $estado, string $nombre, string $codigo, string $grupo_codigo): array {
      $fn_retornar_parametro_estado = function(bool $estado): array {
        return array(":estado" => boolval($estado));
      };
      $fn_retornar_parametro_nombre = function(string $nombre): array {
        $control = (isset($nombre) && is_string($nombre) && (strlen($nombre) > 0));
        return ($control) ? (array(":nombreUsuario" => (strtoupper($nombre) . "%"))) : (array());
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
      $dao = new UsuarioEmpresaDAO();
      $parametros = $this->retornar_parametros_consulta($estado, $nombre, $codigo, $grupo_codigo);
      $parametros_consulta = $this->cambiar_parametro_grupo($grupo_codigo, $parametros);
      $cantidad_tuplas = $dao->contar_tuplas($parametros_consulta);
      $ventana = $util->retornar_ventana($pagina, 128);
      $lista = $dao->buscar_parametros($ventana, 128, $parametros_consulta);
      $lista = $this->convertir_tuplas_lista($lista);
      $lista = $this->filtrar_lista_retorno($lista);
      $resultado = $util->retornar_lista("/ruta/lista.php?", $pagina, 128, $cantidad_tuplas, $parametros);
      $resultado["elementos"] = $lista;
      return $resultado;
    }
  }
?>
