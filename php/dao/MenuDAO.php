<?php
  require_once __DIR__ . "/../modelo/Menu.php";
  require_once __DIR__ . "/../conexion/Conexion.php";
  require_once __DIR__ . "/UtilDAO.php";

  class MenuDAO {

    public function guardar(Menu $menu): int {
      $conexion = new Conexion();
      $consulta = "INSERT INTO menu(nombre, estado, grupo_id) VALUES (:nombre, TRUE, :idGrupo);";
      $parametros = array(
        ":nombre" => $menu->__get("nombre"),
        ":idGrupo" => $menu->__get("grupo")->id
      );
      $id = $conexion->ejecutar_consulta_guardar($consulta, $parametros);
      return $id;
    }

    public function agregar_codigo(int $id, string $codigo): void {
      $conexion = new Conexion();
      $consulta = "UPDATE menu SET menu_codigo = :codigo WHERE (estado IS TRUE) AND (menu_id = :id);";
      $parametros = array(
        ":id" => $id,
        ":codigo" => $codigo
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function actualizar(Menu $menu): void {
      $conexion = new Conexion();
      $consulta = "UPDATE menu SET nombre = :nombre, grupo_id = :idGrupo WHERE (estado IS TRUE) AND (menu_id = :id);";
      $parametros = array(
        ":id" => $menu->__get("menu_id"),
        ":nombre" => $menu->__get("nombre"),
        ":idGrupo" => $menu->__get("grupo")->id
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function conmutar(Menu $menu): void {
      $conexion = new Conexion();
      $consulta = "UPDATE menu SET estado = :estado WHERE (menu_id = :id);";
      $parametros = array(
        ":id" => $menu->__get("menu_id"),
        ":estado" => intval($menu->__get("estado"))
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function eliminar(Menu $menu): void {
      $conexion = new Conexion();
      $consulta = "DELETE FROM menu WHERE (menu_id = :id);";
      $parametros = array(
        ":id" => $menu->__get("menu_id")
      );
      $conexion->ejecutar_consulta_abml($consulta, $parametros);
    }

    public function buscar_id(int $id): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM menu WHERE (menu_id = :id) LIMIT 1;";
      $parametros = array(
        ":id" => $id
      );
      $tupla = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $tupla;
    }

    public function buscar_codigo(string $codigo): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM menu WHERE (menu_codigo = :codigo) LIMIT 1;";
      $parametros = array(
        ":codigo" => $codigo
      );
      $tupla = $conexion->ejecutar_consulta_lectura_unica($consulta, $parametros);
      return $tupla;
    }

    public function listar_grupo(int $grupo_id): array {
      $conexion = new Conexion();
      $consulta = "SELECT * FROM menu WHERE (estado IS TRUE) AND (grupo_id = :idGrupo);";
      $parametros = array(
        ":idGrupo" => $grupo_id
      );
      $lista = $conexion->ejecutar_consulta_lectura_lista($consulta, $parametros);
      return $lista;
    }

    private function retornar_parametros_consulta(array $parametros): array {
      $fn_retornar_parametro_estado = function(array $parametros): string {
        return (array_key_exists(":estado", $parametros)) ? ("(estado = :estado)") : ("");
      };
      $fn_retornar_parametro_codgio = function(array $parametros): string {
        return (array_key_exists(":codigo", $parametros)) ? ("(menu_codigo = :codigo)") : ("");
      };
      $fn_retornar_parametro_nombre = function(array $parametros): string {
        return (array_key_exists(":nombre", $parametros)) ? ("(nombre LIKE :nombre)") : ("");
      };
      $fn_retornar_parametro_grupo = function(array $parametros): string {
        return (array_key_exists(":grupo", $parametros)) ? ("(grupo_id = :grupo)") : ("");
      };
      $clausulas = array(
        $fn_retornar_parametro_estado($parametros),
        $fn_retornar_parametro_codgio($parametros),
        $fn_retornar_parametro_nombre($parametros),
        $fn_retornar_parametro_grupo($parametros)
      );
      return $clausulas;
    }

    public function contar_tuplas(array $parametros): int {
      $fn_retornar_consulta = function(string $clausulas): string {
        $consulta = "SELECT COUNT(*) AS cantidad FROM menu WHERE " . $clausulas . ";";
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
        $consulta = "SELECT * FROM menu WHERE " . $clausulas . " ORDER BY nombre LIMIT " . $limite . " OFFSET " . $ventana . ";";
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
