<?php
namespace fingerprint;

require("../core/querydb.php");
require_once("../core/helpers/helpers.php");

header('Content-Type: application/json');
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$usuario = isset($_POST['empleado']) && $_POST['empleado'] !== 'todas' ? $_POST['empleado'] : null;



echo getRegistrosPorUsuarioYFecha($fecha, $usuario);
