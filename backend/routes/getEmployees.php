<?php
require_once __DIR__ . '/../config/config.php';
include_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../controllers/employees.controller.php";

$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];


if($REQUEST_METHOD === "GET"){
    $idParams = isset($_GET["id"]) ? $_GET["id"] : null;
    if(!$idParams) {

        $response = getEmployees($pdo);
    } else {
        
        $response = getEmployee($idParams, $pdo);
    }


    if (!$response["success"]){
        http_response_code(500);
        $response = [
            "error" => $response["error"]
        ];
    } else {
        http_response_code(200);
        $response = [
            "data" => $response["data"]
        ];
    }
    echo json_encode($response);
}


if($REQUEST_METHOD === "POST"){

    $formDetails = ["firstName", "lastName", "birthDate", "gender", "email", "phoneNumber", "address", "hireDate", "employmentStatus", "jobTitle", "password", "isAdmin"];

    foreach($formDetails as $field){
        if(!isset($_POST[$field]) || trim($_POST[$field]) === ""){
            http_response_code(422);
            echo json_encode([
                "error" => "Incomplete information",
                "message" => "The $field is missing"
            ]);
            return;
        }
    }   

    $response = insertEmployee($_POST, $pdo);

    if(!$response["success"]){
        http_response_code(500);
        echo json_encode([
                "error" => $response["message"]
            ]);
        return;
    }
    http_response_code(200);
    echo json_encode([
        "message" => $response["message"]
    ]);
    
}

?>
