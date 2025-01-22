<?php
  require_once __DIR__ . "/../../servicio/ServicioGrupo.php";
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
    $control_nombre = isset($_GET["nombre"]) && is_string($_GET["nombre"]);
    $control_codigo = isset($_GET["codigo"]) && is_string($_GET["codigo"]);
    $control_pagina = isset($_GET["pagina"]) && is_numeric($_GET["pagina"]) && (intval($_GET["pagina"]) > 0);
    return $control_estado && $control_nombre && $control_codigo && $control_pagina;
  }

  $util = new UtilControlador();
  $servicio_grupo = new ServicioGrupo();
  $control = $util->controlar_tipo_consulta_get($_SERVER["REQUEST_METHOD"]);
  interrumpir_ejecucion(!$control, "error de tipo de petición");
  $control = controlar_datos();
  interrumpir_ejecucion(!$control, "error de datos");
  $resultado = $servicio_grupo->buscar_parametros(
    boolval($_GET["estado"]),
    strval($_GET["nombre"]),
    strval($_GET["codigo"]),
    intval($_GET["pagina"])
  );
  $util->enviar_respuesta_exitosa("operacion exitosa", $resultado);
  exit(0);
?>
