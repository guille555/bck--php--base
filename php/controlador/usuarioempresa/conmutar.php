<?php
  require_once __DIR__ . "/../../servicio/ServicioUsuarioEmpresa.php";
  require_once __DIR__ . "/../UtilControlador.php";

  function interrumpir_ejecucion(bool $control, string $mensaje): void {
    $util = new UtilControlador();
    if ($control) {
      $util->enviar_respuesta_erronea($mensaje);
      exit(11);
    }
  }

  function controlar_datos(array $datos): bool {
    $control_codigo = isset($datos["codigo"]) && is_string($datos["codigo"]) && (strlen($datos["codigo"]) > 0);
    return $control_codigo;
  }

  $util = new UtilControlador();
  $servicio_usuario_empresa = new ServicioUsuarioEmpresa();
  $control = $util->controlar_tipo_consulta_post($_SERVER["REQUEST_METHOD"]);
  interrumpir_ejecucion(!$control, "error de tipo de petición");
  $datos = $util->retornar_datos_post();
  $control = controlar_datos($datos);
  interrumpir_ejecucion(!$control, "error de datos");
  $resultado = $servicio_usuario_empresa->conmutar($datos);
  $control = $util->controlar_resultado($resultado);
  interrumpir_ejecucion(!$control, "falla en la operación");
  $util->enviar_respuesta_exitosa("operacion exitosa", null);
  exit(0);
?>
