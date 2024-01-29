<?php
namespace fingerprint;

require("../core/querydb.php");
require_once("../core/helpers/helpers.php");

header('Content-Type: application/json');

$username = isset($_POST['username']) ? $_POST['username'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if ($username !== null && $password !== null) {
    $resultado = updatePassword($username, $password);

    echo json_encode($resultado);
} else {
    echo json_encode(['error' => 'Faltan el nombre de usuario o la contraseña.']);
}


