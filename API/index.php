<?php

require_once dirname(__FILE__).'/../API_Models/BaseResponse.php';

use API_Models\BaseResponse;

$path = $_SERVER['PATH_INFO'];
$splitPath = explode("/", $path);
$controllerName = "\\".ucfirst($splitPath[1])."Controller";
$apiFunction = ucfirst($splitPath[2]);
$modelsNamespace = "API_Models";
$requestModelName = "\\".$modelsNamespace."\\".ucfirst($splitPath[1])."\\".ucfirst($splitPath[2])."Request";
$responseModelName = "\\".$modelsNamespace."\\".ucfirst($splitPath[1])."\\".ucfirst($splitPath[2])."Response";
$apiFolder = dirname(__FILE__);
$controllerFileName = $apiFolder."/".ucfirst($splitPath[1])."Controller.php";
$requestModelFileName = "$apiFolder/..".str_replace("\\", "/", $requestModelName).".php";
$responseModelFileName = "$apiFolder/..".str_replace("\\", "/", $responseModelName).".php";

require_once $controllerFileName;
require_once $requestModelFileName;
require_once $responseModelFileName;

$controller = new $controllerName();
$request = new $requestModelName();
/**
 * @var $response BaseResponse
 */
$response = $controller->$apiFunction($request);
$statusCode = $response->getStatusCode();
http_response_code($statusCode);

if ($statusCode != 200) {
    exit();
}

header("Content-type: application/json");
echo json_encode($response);
