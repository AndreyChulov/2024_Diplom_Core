<?php

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/StartRequest.php";
require_once "$currentFolder/../API_Models/Game/StartResponse.php";
require_once "$currentFolder/../API_Models/Game/GameInfoRequest.php";
require_once "$currentFolder/../API_Models/Game/GameInfoResponse.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../Classes/Game/Board.php";
require_once "$currentFolder/../Classes/Game/BoardInitializeType.php";

use OpenApi\Attributes as OA;
use Classes\Game\Board;
use Classes\Game\BoardInitializeType;
use API_Models\Game\StartRequest;
use API_Models\Game\StartResponse;
use API_Models\Game\GameInfoRequest;
use API_Models\Game\GameInfoResponse;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../Classes/Globals.php";
require_once "$currentFolder/../Classes/Strings.php";

use Classes\Globals;
use Classes\Strings;

#[OA\Tag(
    name: 'Game',
    description: 'Операции связанные с процессом игры'
)]
class GameController
{
    #[OA\Post(
        path: '/api/game/start',
        summary: "Начало игры",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Game\StartRequest::class
                )
            )
        ),
        tags: ['Game'],
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
                response:"401",
                description: "Unauthorized - Неверный пароль/hash"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"403",
                description: <<<DESCRIPTION
                        Forbidden - нельзя создать новую игру, текущая активная игра уже существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\StartResponse::class
                )
            )
        ]
    )]
    public function Start(StartRequest $request): StartResponse{
        if ($request->getIsGetRequest()){
            return new StartResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new StartResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new StartResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new StartResponse(401, Strings::$KEY_WRONG);
        }

        if ($database->IsGameExist($login)){
            return new StartResponse(403, Strings::$LOGIN_ALREADY_EXISTS);
        }

        $board = new Board(BoardInitializeType::GAME_START);
        $serializedBoard = json_encode($board);
        $gameKey = dechex(rand()).dechex(rand()).dechex(rand());

        $database->CreateGame($login, $gameKey, $serializedBoard);

        return new StartResponse(200, Strings::$GAME_STARTED, $gameKey);
    }

    #[OA\Get(
        path: '/api/game/gameInfo',
        summary: <<<SUMMARY
            Получение статуса игры 
            (Вызов значительно тяжелее для сервера, по сравнению с остальными, 
            использовать только для начальной прорисовки игры)
        SUMMARY,
        tags: ['Game'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/GameInfo_LoginParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/GameInfo_KeyParameter"
            )
        ],
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
                response:"401",
                description: "Unauthorized - Неверный пароль/hash"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"403",
                description: <<<DESCRIPTION
                        Forbidden - нельзя получить данные игры, текущая активная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\GameInfoResponse::class
                )
            )
        ]
    )]
    public function GameInfo(GameInfoRequest $request): GameInfoResponse{
        if ($request->getIsPostRequest()){
            return new GameInfoResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new GameInfoResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new GameInfoResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new GameInfoResponse(401, Strings::$KEY_WRONG);
        }

        if (!$database->IsGameExist($login)){
            return new GameInfoResponse(403, Strings::$LOGIN_ALREADY_EXISTS);
        }

        $gameInfoDataModel = $database->GetGameInfo($login);

        return new GameInfoResponse(200, Strings::$GAME_INFO_RETRIEVED, $gameInfoDataModel);
    }
}