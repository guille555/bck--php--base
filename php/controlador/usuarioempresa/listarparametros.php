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

  function controlar_datos(): bool {
    $control_estado = isset($_GET["estado"]);
    $control_nombre_usuario = isset($_GET["nombreusuario"]) && is_string($_GET["nombreusuario"]);
    $control_codigo = isset($_GET["codigo"]) && is_string($_GET["codigo"]);
    $control_grupo = isset($_GET["grupo"]) && is_string($_GET["grupo"]);
    $control_pagina = isset($_GET["pagina"]) && is_numeric($_GET["pagina"]) && (intval($_GET["pagina"]) > 0);
    return $control_estado && $control_nombre_usuario && $control_codigo && $control_grupo && $control_pagina;
  }

  $util = new UtilControlador();
  $servicio_usuario_empresa = new ServicioUsuarioEmpresa();
  $control = $util->controlar_tipo_consulta_get($_SERVER["REQUEST_METHOD"]);
  interrumpir_ejecucion(!$control, "error de tipo de petición");
  $control = controlar_datos();
  interrumpir_ejecucion(!$control, "error de datos");
  $resultado = $servicio_usuario_empresa->buscar_parametros(
    boolval($_GET["estado"]),
    strval($_GET["nombreusuario"]),
    strval($_GET["codigo"]),
    strval($_GET["grupo"]),
    intval($_GET["pagina"])
  );
  $util->enviar_respuesta_exitosa("operacion exitosa", $resultado);
  exit(0);
?>
