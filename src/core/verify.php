<?php
namespace fingerprint;

require_once("../core/helpers/helpers.php");
require_once("../core/querydb.php");

if (!empty($_POST["data"])) {
    //echo "Data received successfully.\n"; 

    $user_data = json_decode($_POST["data"], true); 
    if (is_null($user_data)) {
        //echo "Failed to decode JSON.\n";
        exit;
    }

    if (!isset($user_data['index_finger'][0])) {
        //echo "Index finger data is missing.\n";
        exit;
    }
    $pre_reg_fmd_string = $user_data['index_finger'][0];

    $plaza = 29; 
    $hand_data = json_decode(getUserFmds($plaza), true);
    if (empty($hand_data)) {
        //echo "No fingerprint data found for plaza {$plaza}.\n";
        exit;
    }

    $matchFound = false;

    foreach ($hand_data as $fingerprint_data) {
        //echo "Checking fingerprint for user ID: {$fingerprint_data['username']}.\n"; 

        $enrolled_fingers = [
            "index_finger" => $fingerprint_data['indexfinger'],
            "middle_finger" => $fingerprint_data['middlefinger']
        ];

        try {
            $json_response = verify_fingerprint($pre_reg_fmd_string, $enrolled_fingers);
            $response = json_decode($json_response);

            if ($response === "match") {
                //echo "Fingerprint match found for user ID: {$fingerprint_data['username']}.\n"; // Mensaje de depuración
                $matchFound = true;
                $matchedUserId = $fingerprint_data['username']; 
                //echo "usuario a traer: ". $matchedUserId;
                createRecord($matchedUserId); 
                echo getUserDetails($matchedUserId); 
                break; 
            }
        } catch (\Exception $e) {
            echo "Error during fingerprint verification: " . $e->getMessage() . "\n"; // Manejo de errores
        }
    }

    if (!$matchFound) {
        echo "No fingerprint match found.\n"; // Mensaje de depuración
        echo json_encode("failed"); 
    }
} else {
    echo "Post request with 'data' field required.\n";
}
