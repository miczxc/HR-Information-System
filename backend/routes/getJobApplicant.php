<?php
// require_once __DIR__ . '/../config/config.php';
include_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../controllers/getJobApplicant.controller.php";


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");


$REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];


if($REQUEST_METHOD === "GET"){
    $idParams = isset($_GET["id"]) ? $_GET["id"] : null;
    if(!$idParams) {

        $response = getJobApplicants($pdo);
    } else {
        
        $response = getJobApplicant($idParams, $pdo);
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

?>
