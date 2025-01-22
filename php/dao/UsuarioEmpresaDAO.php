<?php
  require_once __DIR__ . "/../modelo/UsuarioEmprersa.php";
  require_once __DIR__ . "/../conexion/Conexion.php";
  require_once __DIR__ . "/UtilDAO.php";

  class UsuarioEmpresaDAO {

    public function guardar(UsuarioEmpresa $usuario_empresa): int {
      $conexion = new Conexion();
      $consulta = "INSERT INTO usuario_empresa(nombre_usuario, clave, estado, grupo_id) VALUES (:nombreUsuario, :clave, TRUE, :idGrupo);";
      $parametros = array(
        ":nombreUsuario" => $usuario_empresa->__get("nombre_usuario"),
        ":clave" => $usuario_empresa->__get("clave"),
        ":idGrupo" => $usuario_empresa->__get("grupo")->id
      );
      $id = $conexion->ejecutar_consulta_guardar($consulta, $parametros);
      return $id;
    }

    public function agregar_codigo(int $id, string $codigo): void {
      $conexion = new Conexion();
      $consulta = "UPDATE usuario_empresa SET usuario_empresa_codigo = :codigo WHERE (estado IS TRUE) AND (usuario_empresa_id = :id);";
      $parametros = array(
        ":id" => $id,
        ":codigo" => $codigo
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function actualizar(UsuarioEmpresa $usuario_empresa): void {
      $conexion = new Conexion();
      $consulta = "UPDATE usuario_empresa SET nombre_usuario = :nombreUsuario, clave = :clave, grupo_id = :idGrupo WHERE (estado IS TRUE) AND (usuario_empresa_id = :id);";
      $parametros = array(
        ":id" => $usuario_empresa->__get("usuario_empresa_id"),
        ":nombreUsuario" => $usuario_empresa->__get("nombre_usuario"),
        ":clave" => $usuario_empresa->__get("clave"),
        ":idGrupo" => $usuario_empresa->__get("grupo")->id
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function conmutar(UsuarioEmpresa $usuario_empresa): void {
      $conexion = new Conexion();
      $consulta = "UPDATE usuario_empresa SET estado = :estado WHERE (usuario_empresa_id = :id);";
      $parametros = array(
        ":id" => $usuario_empresa->__get("usuario_empresa_id"),
        ":estado" => intval($usuario_empresa->__get("estado"))
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function eliminar(UsuarioEmpresa $usuario_empresa): void {
      $conexion = new Conexion();
      $consulta = "DELETE FROM usuario_empresa WHERE (usuario_empresa_id = :id);";
      $parametros = array(
        ":id" => $usuario_empresa->__get("usuario_empresa_id")
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function buscar_id(int $id): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM usuario_empresa WHERE (usuario_empresa_id = :id) LIMIT 1;";
      $parametros = array(
        ":id" => $id
      );
      $tupla = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $tupla;
    }

    public function buscar_codigo(string $codigo): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM usuario_empresa WHERE (usuario_empresa_codigo = :codigo) LIMIT 1;";
      $parametros = array(
        ":codigo" => $codigo
      );
      $tupla = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $tupla;
    }

    private function retornar_parametros_consulta(array $parametros): array {
      $fn_retornar_parametro_estado = function(array $parametros): string {
        return (array_key_exists(":estado", $parametros)) ? ("(estado = :estado)") : ("");
      };
      $fn_retornar_parametro_codgio = function(array $parametros): string {
        return (array_key_exists(":codigo", $parametros)) ? ("(usuario_empresa_codigo = :codigo)") : ("");
      };
      $fn_retornar_parametro_nombre_usuario = function(array $parametros): string {
        return (array_key_exists(":nombreUsuario", $parametros)) ? ("(nombre_usuario LIKE :nombreUsuario)") : ("");
      };
      $fn_retornar_parametro_grupo = function(array $parametros): string {
        return (array_key_exists(":grupo", $parametros)) ? ("(grupo_id = :grupo)") : ("");
      };
      $clausulas = array(
        $fn_retornar_parametro_estado($parametros),
        $fn_retornar_parametro_codgio($parametros),
        $fn_retornar_parametro_nombre_usuario($parametros),
        $fn_retornar_parametro_grupo($parametros)
      );
      return $clausulas;
    }

    public function contar_tuplas(array $parametros): int {
      $fn_retornar_consulta = function(string $clausulas): string {
        $consulta = "SELECT COUNT(*) AS cantidad FROM usuario_empresa WHERE " . $clausulas . ";";
        return $consulta;
      };
      $util = new UtilDAO();
      $conexion = new Conexion();
      $clausulas = $this->retornar_parametros_consulta($parametros);
      $clausulas_consulta = $util->retornar_clausulas($clausulas);
      $consulta = $fn_retornar_consulta($clausulas_consulta);
      $resultado = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      $resultado = $util->controlar_tupla_resultado($resultado);
      $resultado = $resultado["cantidad"];
      return $resultado;
    }

    public function buscar_parametros(int $ventana, int $limite, array $parametros): array {
      $fn_retornar_consulta = function(string $clausulas, int $ventana, int $limite): string {
        $consulta = "SELECT * FROM usuario_empresa WHERE " . $clausulas . " ORDER BY nombre_usuario LIMIT " . $limite . " OFFSET " . $ventana . ";";
        return $consulta;
      };
      $util = new UtilDAO();
      $conexion = new Conexion();
      $clausulas = $this->retornar_parametros_consulta($parametros);
      $clausulas_consulta = $util->retornar_clausulas($clausulas);
      $consulta = $fn_retornar_consulta($clausulas_consulta, $ventana, $limite);
      $elementos = $conexion->ejecutar_consulta_lectura_lista($consulta, $parametros);
      return $elementos;
    }
  }
?>
