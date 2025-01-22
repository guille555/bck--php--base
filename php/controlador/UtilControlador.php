<?php
  require_once __DIR__ . "/../util/EstadoSistema.php";

  class UtilControlador {

    public function controlar_tipo_consulta_get(string $tipo_consulta): bool {
      return ("GET" === $tipo_consulta);
    }

    public function controlar_tipo_consulta_post(string $tipo_consulta): bool {
      return ("POST" === $tipo_consulta);
    }

    public function retornar_datos_post(): array {
      $datos = file_get_contents("php://input");
      $resultado = json_decode($datos, true);
      return $resultado;
    }

    public function controlar_resultado(int $codigo): bool {
      return (EstadoSistema::OPERACION_EXITOSA->value === $codigo);
    }

    public function enviar_respuesta_exitosa(string $mensaje, mixed $elementos): void {
      $fn_retornar_configuracion = function(string $mensaje, mixed $elementos): array {
        $configuracion = array(
          "estado" => true,
          "codigo" => 200,
          "mensaje" => $mensaje,
          "contenido" => $elementos
        );
        return $configuracion;
      };
      http_response_code(200);
      $respuesta = $fn_retornar_configuracion($mensaje, $elementos);
      echo json_encode($respuesta);
    }

    public function enviar_respuesta_erronea(string $mensaje): void {
      $fn_retornar_configuracion = function(string $mensaje): array {
        $configuracion = array(
          "estado" => false,
          "codigo" => 500,
          "mensaje" => $mensaje,
          "contenido" => null
        );
        return $configuracion;
      };
      http_response_code(500);
      $respuesta = $fn_retornar_configuracion($mensaje);
      echo json_encode($respuesta);
    }
  }
?>
