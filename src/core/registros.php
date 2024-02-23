<?php
namespace fingerprint;

require("../core/querydb.php");
require_once("../core/helpers/helpers.php");

header('Content-Type: application/json');
$fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : null;
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : null;
$usuario = isset($_POST['empleado']) && $_POST['empleado'] !== 'todas' ? $_POST['empleado'] : null;

echo getRegistrosPorUsuarioYFecha($fechaInicio, $fechaFin, $usuario);