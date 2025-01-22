<?php

  class Conexion {

    private function obtener_conexion(object $configuracion): object {
      try {
        $ruta = "mysql:host=" . $configuracion->ruta . ":" . strval($configuracion->puerto) . ";dbname=" . $configuracion->base_datos;
        $conexion = new PDO($ruta, $configuracion->usuario, $configuracion->clave);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexion->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        return $conexion;
      } catch (PDOException $exc) {
        $conexion = new stdClass();
        return $conexion;
      }
    }

    private function controlar_conexion(object $conexion): void {
      ($conexion instanceof stdClass) && (throw new Exception("error en la conexion", 11));
    }

    private function obtener_conexion_lectura(): object {
      $configuracion = new stdClass();
      $configuracion->ruta = "localhost";
      $configuracion->puerto = 0000;//puerto de base de datos
      $configuracion->base_datos = "bd";//nombre de la base de datos
      $configuracion->usuario = "usuario";//nombre del usuario, de base de datos, para realizar lecturas
      $configuracion->clave = "clave";//clave del usuario, de base de datos, para realizar lecturas
      $conexion = $this->obtener_conexion($configuracion);
      $this->controlar_conexion($conexion);
      return $conexion;
    }

    private function obtener_conexion_abml(): object {
      $configuracion = new stdClass();
      $configuracion->ruta = "localhost";
      $configuracion->puerto = 0000;//puerto de base de datos
      $configuracion->base_datos = "bd";//nombre de la base de datos
      $configuracion->usuario = "usuario";//nombre del usuario, de base de datos, para realizar operaciones ABML
      $configuracion->clave = "clave";//clave del usuario, de base de datos, para realizar operaciones ABML
      $conexion = $this->obtener_conexion($configuracion);
      $this->controlar_conexion($conexion);
      return $conexion;
    }

    public function ejecutar_consulta_guardar(string $consulta, array $parametros): int {
      $conexion = null;
      $cursor = null;
      try {
        $conexion = $this->obtener_conexion_abml();
        $conexion->beginTransaction();
        $cursor = $conexion->prepare($consulta);
        $cursor->execute($parametros);
        $id = $conexion->lastInsertId();
        $conexion->commit();
        $cursor->closeCursor();
        return $id;
      } catch (PDOException $exc) {
        $conexion->rollBack();
        throw $exc;
      } catch (Exception $exc) {
        throw $exc;
      } finally {
        $cursor = null;
        $conexion = null;
        unset($cursor, $conexion);
      }
    }

    public function ejecutar_consulta_abml(string $consulta, array $parametros): void {
      $conexion = null;
      $cursor = null;
      try {
        $conexion = $this->obtener_conexion_abml();
        $conexion->beginTransaction();
        $cursor = $conexion->prepare($consulta);
        $cursor->execute($parametros);
        $conexion->commit();
        $cursor->closeCursor();
      } catch (PDOException $exc) {
        $conexion->rollBack();
        throw $exc;
      } catch (Exception $exc) {
        throw $exc;
      } finally {
        $cursor = null;
        $conexion = null;
        unset($cursor, $conexion);
      }
    }

    public function ejecutar_consulta_lectura_unica(string $consulta, array $parametros): array {
      $conexion = null;
      $cursor = null;
      $fn_controlar_tupla = function(mixed $tupla): array {
        return (is_array($tupla)) ? ($tupla) : (array());
      };
      try {
        $conexion = $this->obtener_conexion_lectura();
        $cursor = $conexion->prepare($consulta);
        $cursor->execute($parametros);
        $cursor->setFetchMode(PDO::FETCH_ASSOC);
        $tupla = $cursor->fetch();
        $tupla = $fn_controlar_tupla($tupla);
        $cursor->closeCursor();
        return $tupla;
      } catch (Exception $exc) {
        return array();
      } finally {
        $cursor = null;
        $conexion = null;
        unset($cursor, $conexion);
      }
    }

    public function ejecutar_consulta_lectura_lista(string $consulta, array $parametros): array {
      $conexion = null;
      $cursor = null;
      try {
        $conexion = $this->obtener_conexion_lectura();
        $cursor = $conexion->prepare($consulta);
        $cursor->execute($parametros);
        $cursor->setFetchMode(PDO::FETCH_ASSOC);
        $tuplas = $cursor->fetchAll();
        $cursor->closeCursor();
        return $tuplas;
      } catch (Exception $exc) {
        return array();
      } finally {
        $cursor = null;
        $conexion = null;
        unset($cursor, $conexion);
      }
    }
  }
?>
