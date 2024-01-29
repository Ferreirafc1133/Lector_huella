<?php


namespace fingerprint;
require_once("database.php");

function setUserFmds($user_id, $index_finger_fmd_string, $middle_finger_fmd_string){
    $myDatabase = new database();
    $sql_query = "update users set indexfinger=?, middlefinger=? WHERE username=?";
    $param_type = "sss";
    $param_array = [$index_finger_fmd_string, $middle_finger_fmd_string, $user_id];
    $affected_rows = $myDatabase->update($sql_query, $param_type, $param_array);

    if($affected_rows > 0){
        return "success";
    }
    else{
        return "failed in querydb";
    }
}
function getAllUsernames() {
    $myDatabase = new database();
    $sql_query = "SELECT username FROM users";
    $usernames = $myDatabase->select($sql_query);
    return json_encode($usernames); // Devuelve solo JSON
}

/*
function getRegistrosPorUsuarioYFecha($fecha, $usuario) {
    $myDatabase = new database();
    $sql_query = "";
    $param_array = [];

    if ($usuario) {
        // Consulta para un usuario específico
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE Usuario = ? AND DATE(Registro) = ?
                      GROUP BY Usuario";
        $param_array = [$usuario, $fecha];
    } else {
        // Consulta para todos los usuarios
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE DATE(Registro) = ?
                      GROUP BY Usuario";
        $param_array = [$fecha];
    }

    $registros = $myDatabase->select($sql_query, "s", $param_array);
    return json_encode($registros);
}
*/

function getRegistrosPorUsuarioYFecha($fecha, $usuario) {
    $myDatabase = new database();
    $sql_query = "";

    if (!$usuario ) {
        // Consulta para todos los usuarios
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE DATE(Registro) = ?
                      GROUP BY Usuario";
        $param_array = [$fecha];
        $param_type = "s"; // Un solo parámetro de tipo string
    } else {
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE Usuario = ? AND DATE(Registro) = ?
                      GROUP BY Usuario";
        $param_array = [$usuario, $fecha];
        $param_type = "ss";
    }

    error_log("Consulta SQL: " . $sql_query);
    error_log("Parámetros: " . print_r($param_array, true));

    $registros = $myDatabase->select($sql_query, $param_type, $param_array);

    if (empty($registros)) {
        error_log("No se encontraron registros para los parámetros dados.");
        return json_encode(["error" => "No hay registros para los parámetros dados."]);
    } else {
        return json_encode($registros);
    }
}



function createRecord($user_id) {
    $myDatabase = new database();
    date_default_timezone_set('America/Mexico_City');
    $currentDateTime = date('Y-m-d H:i:s'); 
    $sql_query = "INSERT INTO registros (Usuario, Registro) VALUES (?, ?)";
    $param_type = "ss"; 
    $param_array = [$user_id, $currentDateTime];
    $affected_rows = $myDatabase->insert($sql_query, $param_type, $param_array);
    if($affected_rows > 0){
        return "success";
    }
    else{
        return "failed in querydb";
    }
}    
function getUserFmds($user_id){
    $myDatabase = new database();
    $sql_query = "select indexfinger, middlefinger from users WHERE username=?";
    $param_type = "s";
    $param_array = [$user_id];
    $fmds = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($fmds);
}

function getUserDetails($user_id){
    $myDatabase = new database();
    $sql_query = "select username, fullname from users WHERE username=?";
    $param_type = "s";
    $param_array = [$user_id];
    $user_info = $myDatabase->select($sql_query, $param_type, $param_array);
    return json_encode($user_info);
}

function getAllFmds(){
    $myDatabase = new database();
    $sql_query = "select indexfinger, middlefinger from users WHERE indexfinger != ''";
    $allFmds = $myDatabase->select($sql_query);
    return json_encode($allFmds);
}

function comprobarLogin($username, $password) {
    session_start();
    $myDatabase = new database();
    $sql_query = "SELECT password FROM users WHERE username=?";
    $param_type = "s";
    $param_array = [$username];
    $result = $myDatabase->select($sql_query, $param_type, $param_array);

    if ($result && count($result) > 0) {
        $hashed_password = $result[0]['password']; 
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user'] = true;
            return json_encode(["success" => "Inicio de sesión correcto."]);
        } else {
            return json_encode(["error" => "Clave incorrecta."]);
        }
    } else {
        return json_encode(["error" => "Usuario no encontrado."]);
    }
}
function updatePassword($username, $newPassword) {
    $myDatabase = new database();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sql_query = "UPDATE users SET password = ? WHERE username = ?";
    $param_type = "ss";
    $param_array = [$hashedPassword, $username];

    $affected_rows = $myDatabase->update($sql_query, $param_type, $param_array);

    if ($affected_rows > 0) {
        return ["success" => "La contraseña ha sido actualizada correctamente."];
    } else {
        return ["error" => "No se pudo actualizar la contraseña."];
    }
}