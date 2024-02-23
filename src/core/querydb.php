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

function getRegistrosPorUsuarioYFecha($fechaInicio, $fechaFin, $usuario) {
    $myDatabase = new database();
    $sql_query = "";

    if (!$usuario) {
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE DATE(Registro) BETWEEN ? AND ?
                      GROUP BY Usuario";
        $param_array = [$fechaInicio, $fechaFin];
        $param_type = "ss"; 
    } else {
        $sql_query = "SELECT Usuario, MIN(Registro) AS Entrada, MAX(Registro) AS Salida 
                      FROM registros 
                      WHERE Usuario = ? AND DATE(Registro) BETWEEN ? AND ?
                      GROUP BY Usuario";
        $param_array = [$usuario, $fechaInicio, $fechaFin];
        $param_type = "sss"; 
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
function getUserFmds(){
    $myDatabase = new database();
    $sql_query = "select indexfinger, middlefinger, username from users";
    $fmds = $myDatabase->select($sql_query);
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
    $sql_query = "SELECT password, privilege, plaza FROM users WHERE username=?";
    $param_type = "s";
    $param_array = [$username];
    $result = $myDatabase->select($sql_query, $param_type, $param_array);

    if ($result && count($result) > 0) {
        $hashed_password = $result[0]['password']; 
        $privilege = $result[0]['privilege']; 
        $plaza = $result[0]['plaza'];
        if (password_verify($password, $hashed_password)) {
            $_SESSION['privilege'] = $privilege; 
            $_SESSION['plaza'] = $plaza;
            return ["success" => "Inicio de sesión correcto.", "session" => ["privilege" => $_SESSION['privilege'], "plaza" => $_SESSION['plaza']]];
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

function lectorID($plaza) {
    $myDatabase = new database();
    $sql_query = "select lector_huella from plazas WHERE id_plaza = ?";
    $param_type = "s";
    $param_array = [$plaza];
    $ID = $myDatabase->select($sql_query, $param_type, $param_array);
    return $ID;
}

