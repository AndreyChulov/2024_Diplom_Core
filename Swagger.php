<?php

spl_autoload_register('autoloader');
function autoloader(string $name): void // On-fly classes loader for swagger analyser
{
    if (str_ends_with($name, "Controller")){
        $currentFolder = dirname(__FILE__); // only function scope is accessible
        $modelFilePath = "$currentFolder/API/$name.php";
        if (file_exists($modelFilePath)){
            require_once $modelFilePath;
        }
    } elseif (str_ends_with($name, "Request") or str_ends_with($name, "Response")){
        //WARNING:Not checked for bugs yet
        $currentFolder = dirname(__FILE__); // only function scope is accessible
        $modelFilePath = "$currentFolder/$name.php";
        if (file_exists($modelFilePath)){
            require_once $modelFilePath;
        }
    }
}

require_once "vendor/autoload.php";
require_once "API/OpenApiSpec.php";

use \OpenApi\Generator;

$currentFolder = dirname(__FILE__);
$openapi = Generator::scan(
    ["$currentFolder/API", "$currentFolder/API_Models"],
    ['exclude' => ['tests', 'vendor'], 'pattern' => '*.php']);

$documentation = $openapi->toJson();
$swaggerConfigFile = "$currentFolder/swagger-ui/api.json";
file_put_contents($swaggerConfigFile, $documentation);
$swaggerUiIndexFile = "$currentFolder/swagger-ui/index.inc";

include_once $swaggerUiIndexFile;
