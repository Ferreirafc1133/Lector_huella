<?php
namespace fingerprint;
session_start();
require("../core/querydb.php");
require_once("../core/helpers/helpers.php");

header('Content-Type: application/json');

$plaza = $_SESSION['plaza'];

$lectorId = lectorID($plaza);

if (!empty($lectorId)) {
    echo $lectorId[0]['lector_huella']; 
} else {
    echo json_encode(['error' => 'Lector no encontrado.']);
}