<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["data"])) {
    $data = $_POST["data"];

    // Procesar la información y almacenarla según tus necesidades
    // Puedes usar bases de datos, archivos, etc.

    // Simplemente respondemos "success" si la operación fue exitosa
    echo "success";
} else {
    // Respuesta de error si la solicitud no es válida
    echo "Invalid request.";
}

require("./src/html/home.html");


?>
