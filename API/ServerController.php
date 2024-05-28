<?php

$currentFolder = dirname(__FILE__);
require_once "$currentFolder/../API_Models/Server/InstallRequest.php";
require_once "$currentFolder/../API_Models/Server/InstallResponse.php";
require_once "$currentFolder/../API_Models/Server/InstallStatusRequest.php";
require_once "$currentFolder/../API_Models/Server/InstallStatusResponse.php";

use API_Models\Server\InstallRequest;
use API_Models\Server\InstallResponse;
use API_Models\Server\InstallStatusRequest;
use API_Models\Server\InstallStatusResponse;
use OpenApi\Attributes as OA;

$currentFolder = dirname(__FILE__); // swagger-php почему то теряет старое корректное значение переменной
require_once "$currentFolder/../Classes/DatabaseInitializer.php";
require_once "$currentFolder/../Classes/Status.php";

use Classes\DatabaseInitializer;
use Classes\Status;


#[OA\Tag(
    name: 'Server',
    description: 'Операции связанные с сервером'
)]
class ServerController
{
    #[OA\Post(
        path: '/api/server/install',
        summary: "Устанавливает базу данных сервера или обнуляет ее содержимое",
        tags: ["Server"],
        responses: [
            new OA\Response(
                response:"200",
                description:"OK"
            ),
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            )
        ]
    )]
    public function Install(InstallRequest $request):InstallResponse{
        if ($request->getIsGetRequest()){
            return new InstallResponse(true);
        }

        $databaseInitializer = new DatabaseInitializer();
        $databaseInitializer->InitDatabase();

        return new InstallResponse(false);
    }

    #[OA\Get(
        path: '/api/server/installStatus',
        summary: <<<SUMMARY
                Получает данные об установленных/работающих/неработающих компонентов сервера, 
                требующих внимания при установке сервера на хостинг
            SUMMARY,
        tags: ["Server"],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Server\InstallStatusResponse::class
                )
            )
        ]
    )]
    public function InstallStatus(InstallStatusRequest $request): InstallStatusResponse{
        if ($request->getIsPostRequest()){
            return new InstallResponse(true);
        }

        $status = new Status();

        return new InstallStatusResponse(false, $status);
    }
}