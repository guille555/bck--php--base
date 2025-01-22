<?php
  require_once __DIR__ . "/../modelo/Grupo.php";
  require_once __DIR__ . "/../conexion/Conexion.php";
  require_once __DIR__ . "/UtilDAO.php";

  class GrupoDAO {

    public function guardar(Grupo $grupo): int {
      $conexion = new Conexion();
      $consulta = "INSERT INTO grupo(nombre, estado) VALUES (:nombre, TRUE);";
      $parametros = array(
        ":nombre" => $grupo->__get("nombre")
      );
      $id = $conexion->ejecutar_consulta_guardar($consulta, $parametros);
      return $id;
    }

    public function agregar_codigo(int $id, string $codigo): void {
      $conexion = new Conexion();
      $consulta = "UPDATE grupo SET grupo_codigo = :codigo WHERE (estado IS TRUE) AND (grupo_id = :id);";
      $parametros = array(
        ":id" => $id,
        ":codigo" => $codigo
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function actualizar(Grupo $grupo): void {
      $conexion = new Conexion();
      $consulta = "UPDATE grupo SET nombre = :nombre, WHERE (estado IS TRUE) AND (grupo_id = :id);";
      $parametros = array(
        ":id" => $grupo->__get("grupo_id"),
        ":nombre" => $grupo->__get("nombre")
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function conmutar(Grupo $grupo): void {
      $conexion = new Conexion();
      $consulta = "UPDATE grupo SET estado = :estado WHERE (grupo_id = :id);";
      $parametros = array(
        ":id" => $grupo->__get("grupo_id"),
        ":estado" => intval($grupo->__get("estado"))
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function eliminar(Grupo $grupo): void {
      $conexion = new Conexion();
      $consulta = "DELETE FROM grupo WHERE (grupo_id = :id);";
      $parametros = array(
        ":id" => $grupo->__get("grupo_id")
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function buscar_id(int $id): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM grupo WHERE (grupo_id = :id) LIMIT 1;";
      $parametros = array(
        ":id" => $id
      );
      $resultado = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $resultado;
    }

    public function buscar_codigo(string $codigo): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM grupo WHERE (grupo_codigo = :codigo) LIMIT 1;";
      $parametros = array(
        ":codigo" => $codigo
      );
      $resultado = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $resultado;
    }

    private function retornar_parametros_consulta(array $parametros): array {
      $fn_retornar_parametro_estado = function(array $parametros): string {
        return (array_key_exists(":estado", $parametros)) ? ("(estado = :estado)") : ("");
      };
      $fn_retornar_parametro_codgio = function(array $parametros): string {
        return (array_key_exists(":codigo", $parametros)) ? ("(grupo_codigo = :codigo)") : ("");
      };
      $fn_retornar_parametro_nombre = function(array $parametros): string {
        return (array_key_exists(":nombre", $parametros)) ? ("(nombre LIKE :nombre)") : ("");
      };
      $clausulas = array(
        $fn_retornar_parametro_estado($parametros),
        $fn_retornar_parametro_codgio($parametros),
        $fn_retornar_parametro_nombre($parametros)
      );
      return $clausulas;
    }

    public function contar_tuplas(array $parametros): int {
      $fn_retornar_consulta = function(string $clausulas): string {
        $consulta = "SELECT COUNT(*) AS cantidad FROM grupo WHERE " . $clausulas . ";";
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
        $consulta = "SELECT * FROM grupo WHERE " . $clausulas . " ORDER BY nombre LIMIT " . $limite . " OFFSET " . $ventana . ";";
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
