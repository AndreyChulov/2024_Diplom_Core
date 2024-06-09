<?php

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Support/FinishGameRequest.php";
require_once "$currentFolder/../API_Models/Support/FinishGameResponse.php";

use OpenApi\Attributes as OA;
use API_Models\Support\FinishGameRequest;
use API_Models\Support\FinishGameResponse;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../Classes/Globals.php";
require_once "$currentFolder/../Classes/Strings.php";

use Classes\Globals;
use Classes\Strings;

#[OA\Tag(
    name: 'Support',
    description: 'Операции связанные с процессом поддержки игры администратором'
)]
class SupportController
{
    #[OA\Post(
        path: '/api/support/finishGame',
        summary: "Принудительное завершение игры",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Support\FinishGameRequest::class
                )
            )
        ),
        tags: ['Support'],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Support\FinishGameResponse::class
                )
            )
        ]
    )]
    public function FinishGame(FinishGameRequest $request): FinishGameResponse{
        if ($request->getIsGetRequest()){
            return new FinishGameResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new FinishGameResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new FinishGameResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        $database->ForceFinishGames($login);

        return new FinishGameResponse(200, Strings::$GAME_FORCE_FINISHED);
    }
}