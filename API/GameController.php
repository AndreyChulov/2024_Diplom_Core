<?php

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/StartRequest.php";
require_once "$currentFolder/../API_Models/Game/StartResponse.php";
require_once "$currentFolder/../API_Models/Game/GameInfoRequest.php";
require_once "$currentFolder/../API_Models/Game/GameInfoResponse.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/GameStatusRequest.php";
require_once "$currentFolder/../API_Models/Game/GameStatusResponse.php";
require_once "$currentFolder/../API_Models/Game/GameBoardRequest.php";
require_once "$currentFolder/../API_Models/Game/GameBoardResponse.php";
require_once "$currentFolder/../API_Models/Game/CheckerAcceptableMoviesRequest.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/CheckerAcceptableMoviesResponse.php";
require_once "$currentFolder/../API_Models/Game/AcceptableMoviesRequest.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/AcceptableMoviesResponse.php";
require_once "$currentFolder/../API_Models/Game/SurrenderRequest.php";
require_once "$currentFolder/../API_Models/Game/SurrenderResponse.php";
require_once "$currentFolder/../API_Models/Game/MoveRequest.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Game/MoveResponse.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../Classes/Game/Board.php";
require_once "$currentFolder/../Classes/Game/BoardInitializeType.php";
require_once "$currentFolder/../Classes/GameHistory/GameHistory.php";

use OpenApi\Attributes as OA;
use Classes\Game\Board;
use Classes\Game\BoardInitializeType;
use Classes\GameHistory\GameHistory;
use API_Models\Game\StartRequest;
use API_Models\Game\StartResponse;
use API_Models\Game\GameInfoRequest;
use API_Models\Game\GameInfoResponse;
use API_Models\Game\GameStatusRequest;
use API_Models\Game\GameStatusResponse;
use API_Models\Game\GameBoardRequest;
use API_Models\Game\GameBoardResponse;
use API_Models\Game\CheckerAcceptableMoviesRequest;
use API_Models\Game\CheckerAcceptableMoviesResponse;
use API_Models\Game\AcceptableMoviesRequest;
use API_Models\Game\AcceptableMoviesResponse;
use API_Models\Game\SurrenderRequest;
use API_Models\Game\SurrenderResponse;
use API_Models\Game\MoveRequest;
use API_Models\Game\MoveResponse;

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
                description: <<<DESCRIPTION
                    OK - игра не была создана, игрок подключен к игре ожидающей игрока, 
                    в случае загруженности сервера (массовые запросы на создание игры), 
                    при этом ответе игра может оказаться несозданной,
                    проверить можно через api/game/gameInfo. 
                    В случае ошибочного ответа необходимо вызвать api/game/start еще раз.
                DESCRIPTION,
                content: new OA\JsonContent(
                    ref: \API_Models\Game\StartResponse::class
                )
            ),
            new OA\Response(
                response:"201",
                description:"Created - новая игра создана",
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
            return new StartResponse(403, Strings::$GAME_ALREADY_EXISTS);
        }

        if ($database->IsWaitingForPlayerGameExist($login)){
            //INFO: в случае многопоточного PHP, тут может пройти другой запрос к базе на подключение к игре

            $database->ConnectToGame($login);

            return new StartResponse(200, Strings::$GAME_PLAYER_CONNECTED);
        }

        $board = new Board(BoardInitializeType::GAME_START);
        $serializedBoard = json_encode($board);
        $gameKey = dechex(rand()).dechex(rand()).dechex(rand());

        while ($database->IsGameExistByGameKey($gameKey)){
            $gameKey = dechex(rand()).dechex(rand()).dechex(rand());
        }

        $database->CreateGame($login, $gameKey, $serializedBoard);
        $gameHistory = new GameHistory();
        $gameHistorySerialized = json_encode($gameHistory);
        $database->CreateGameHistory($gameKey, $gameHistorySerialized);

        return new StartResponse(201, Strings::$GAME_NEW_STARTED);
    }

    #[OA\Get(
        path: '/api/game/gameInfo',
        summary: <<<SUMMARY
            Получение информации о текущей активной игре
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
            return new GameInfoResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameInfoDataModel = $database->GetGameInfo($login);

        return new GameInfoResponse(200, Strings::$GAME_INFO_RETRIEVED, $gameInfoDataModel);
    }

    #[OA\Get(
        path: '/api/game/gameStatus',
        summary: <<<SUMMARY
            Получение статуса игры 
        SUMMARY,
        tags: ['Game'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/GameStatus_GameKeyParameter"
            )
        ],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
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
                        Forbidden - нельзя получить данные игры, запрошенная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\GameStatusResponse::class
                )
            )
        ]
    )]
    public function GameStatus(GameStatusRequest $request): GameStatusResponse{
        if ($request->getIsPostRequest()){
            return new GameStatusResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new GameStatusResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $gameKey = $request->getGameKey();
        $database = $globals->getDatabase();

        if (!$database->IsGameExistByGameKey($gameKey)){
            return new GameStatusResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameStatus = $database->GetGameStatusByGameKey($gameKey);

        return new GameStatusResponse(200, Strings::$GAME_INFO_RETRIEVED, $gameStatus);
    }

    #[OA\Get(
        path: '/api/game/gameBoard',
        summary: <<<SUMMARY
            Получение состояния доски игры 
        SUMMARY,
        tags: ['Game'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/GameBoard_GameKeyParameter"
            )
        ],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
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
                        Forbidden - нельзя получить данные игры, запрошенная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\GameBoardResponse::class
                )
            )
        ]
    )]
    public function GameBoard(GameBoardRequest $request): GameBoardResponse{
        if ($request->getIsPostRequest()){
            return new GameBoardResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new GameBoardResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $gameKey = $request->getGameKey();
        $database = $globals->getDatabase();

        if (!$database->IsGameExistByGameKey($gameKey)){
            return new GameBoardResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameBoard = $database->GetGameBoardByGameKey($gameKey);

        return new GameBoardResponse(200, Strings::$GAME_INFO_RETRIEVED, $gameBoard);
    }

    #[OA\Get(
        path: '/api/game/checkerAcceptableMovies',
        summary: <<<SUMMARY
            Получение возможных ходов для пешки
        SUMMARY,
        tags: ['Game'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/CheckerAcceptableMovies_LoginParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/CheckerAcceptableMovies_KeyParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/CheckerAcceptableMovies_GameKeyParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/CheckerAcceptableMovies_CheckerParameter"
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
                        Forbidden - нельзя получить данные игры, запрошенная активная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"412",
                description: <<<DESCRIPTION
                        Precondition Failed - пешка отсутствует на указанном поле 
                            или указана пешка противника
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\CheckerAcceptableMoviesResponse::class
                )
            )
        ]
    )]
    public function CheckerAcceptableMovies(CheckerAcceptableMoviesRequest $request): CheckerAcceptableMoviesResponse{
        if ($request->getIsPostRequest()){
            return new CheckerAcceptableMoviesResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new CheckerAcceptableMoviesResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new CheckerAcceptableMoviesResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new CheckerAcceptableMoviesResponse(401, Strings::$KEY_WRONG);
        }

        if (!$database->IsExactGameExist($login, $request->getGameKey())){
            return new CheckerAcceptableMoviesResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameBoard = $database->GetGameBoard($login);
        $board = new Board(BoardInitializeType::EMPTY_BOARD);
        $board->LoadBoardState(json_decode($gameBoard));
        $chessAddress = $request->getChecker();

        if (!$board->IsCheckerOnBoardCell($chessAddress) ||
            $board->IsBlackCheckerOnAddress($chessAddress) != $database->IsBlackPlayer($login)){
            return new CheckerAcceptableMoviesResponse(412, Strings::$CHECKER_NOT_FOUND);
        }

        $gameHistorySerialized = $database->GetGameHistory($request->getGameKey());
        $gameHistory = new GameHistory();
        $gameHistory->LoadFromJsonObject(json_decode($gameHistorySerialized));
        $movies = $board->GetAvailableCheckerMoves($chessAddress, $gameHistory);

        return new CheckerAcceptableMoviesResponse(
            200, Strings::$GAME_INFO_RETRIEVED, json_encode($movies));
    }

    #[OA\Get(
        path: '/api/game/acceptableMovies',
        summary: <<<SUMMARY
            Получение всех возможных ходов для всех пешек игрока
        SUMMARY,
        tags: ['Game'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/AcceptableMovies_LoginParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/AcceptableMovies_KeyParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/AcceptableMovies_GameKeyParameter"
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
                        Forbidden - нельзя получить данные игры, запрошенная активная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\AcceptableMoviesResponse::class
                )
            )
        ]
    )]
    public function AcceptableMovies(AcceptableMoviesRequest $request): AcceptableMoviesResponse{
        if ($request->getIsPostRequest()){
            return new AcceptableMoviesResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new AcceptableMoviesResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new AcceptableMoviesResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new AcceptableMoviesResponse(401, Strings::$KEY_WRONG);
        }

        if (!$database->IsExactGameExist($login, $request->getGameKey())){
            return new AcceptableMoviesResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameBoard = $database->GetGameBoard($login);
        $board = new Board(BoardInitializeType::EMPTY_BOARD);
        $board->LoadBoardState(json_decode($gameBoard));

        $gameHistorySerialized = $database->GetGameHistory($request->getGameKey());
        $gameHistory = new GameHistory();
        $gameHistory->LoadFromJsonObject(json_decode($gameHistorySerialized));
        $movies = $board->GetAvailableMoves($database->IsBlackPlayer($login), $gameHistory);

        return new AcceptableMoviesResponse(
            200, Strings::$GAME_INFO_RETRIEVED, json_encode($movies));
    }

    #[OA\Post(
        path: '/api/game/surrender',
        summary: <<<SUMMARY
            Позволяет игроку сдаться
        SUMMARY,
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Game\SurrenderRequest::class
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
                description: "Unauthorized - Неверный авторизационный ключ"
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
                        Forbidden - нельзя получить данные игры, запрошенная активная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\SurrenderResponse::class
                )
            )
        ]
    )]
    public function Surrender(SurrenderRequest $request): SurrenderResponse{
        if ($request->getIsGetRequest()){
            return new SurrenderResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new SurrenderResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new SurrenderResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new SurrenderResponse(401, Strings::$KEY_WRONG);
        }

        if (!$database->IsExactGameExist($login, $request->getGameKey())){
            return new SurrenderResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $isBlackPlayer = $database->IsBlackPlayer($login);

        $database->SetPlayerSurrender($login, $request->getGameKey(), $isBlackPlayer);

        return new SurrenderResponse(200, Strings::$GAME_INFO_UPDATED);
    }

    #[OA\Post(
        path: '/api/game/move',
        summary: <<<SUMMARY
            Осуществление хода. Внимание!!! 
            Если на этом ходу было взятие пешки и доступно следующее взятие - ход не переходит к противнику,
            однако запрос на доступные ходы вернет все доступные ходы без учета того, что на этом ходу было взятие.
            Фильтр доступных ходов после такого хода должен осуществляться на клиентской стороне. 
        SUMMARY,
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Game\MoveRequest::class
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
                        Forbidden - нельзя получить данные игры, запрошенная активная игра не существует
                    DESCRIPTION
            ),
            new OA\Response(
                response:"412",
                description: <<<DESCRIPTION
                        Precondition Failed - пешка отсутствует на указанном поле 
                            или указана пешка противника или запрошен неверный/несуществующий ход
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Game\MoveResponse::class
                )
            )
        ]
    )]
    public function Move(MoveRequest $request): MoveResponse{
        if ($request->getIsGetRequest()){
            return new MoveResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new MoveResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new MoveResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new MoveResponse(401, Strings::$KEY_WRONG);
        }

        $gameKey = $request->getGameKey();

        if (!$database->IsExactGameExist($login, $gameKey)){
            return new MoveResponse(403, Strings::$GAME_NOT_EXISTS);
        }

        $gameBoard = $database->GetGameBoardByGameKey($gameKey);
        $board = new Board(BoardInitializeType::EMPTY_BOARD);
        $board->LoadBoardState(json_decode($gameBoard));
        $move = $request->getMove();
        $fromAddress = substr($move, 0, 2);
        $moveType = $move[2];
        $isBlackPlayer = $database->IsBlackPlayer($login);
        $gameStatus = $database->GetGameStatusByGameKey($gameKey);

        $gameHistorySerialized = $database->GetGameHistory($gameKey);
        $gameHistory = new GameHistory();
        $gameHistory->LoadFromJsonObject(json_decode($gameHistorySerialized));

        if (!$board->IsCheckerOnBoardCell($fromAddress) ||
            $board->IsBlackCheckerOnAddress($fromAddress) != $isBlackPlayer ||
            ($gameStatus === Strings::$GAME_STATUS_WHITE_TURN && $isBlackPlayer === true) ||
            ($gameStatus === Strings::$GAME_STATUS_BLACK_TURN && $isBlackPlayer === false) ||
            !$board->IsMoveValid($move, $gameHistory)){
            return new MoveResponse(412, Strings::$MOVE_INVALID);
        }

        $board->ApplyMove($move);

        $gameHistory->AddTurn($move, $isBlackPlayer);
        $gameHistorySerialized = json_encode($gameHistory);
        $database->SetGameHistory($gameKey, $gameHistorySerialized);

        $serializedBoard = json_encode($board);
        $database->SetGameBoard($login, $serializedBoard);

        if ($moveType === "-"){
            $database->SetGameStatus($login, $isBlackPlayer ?
                Strings::$GAME_STATUS_WHITE_TURN : Strings::$GAME_STATUS_BLACK_TURN);
        } else {
            $isPlayerWin = $board->IsPlayerWin($isBlackPlayer);

            if ($isPlayerWin){
                $database->SetGameStatus($login, $isBlackPlayer ?
                    Strings::$GAME_STATUS_BLACK_WIN : Strings::$GAME_STATUS_WHITE_WIN);
                $database->SetGameFinishedTime($login, $gameKey);
            } else {
                if (!$board->IsContinueMoveExist($move, $gameHistory)){
                    $database->SetGameStatus($login, $isBlackPlayer ?
                        Strings::$GAME_STATUS_WHITE_TURN : Strings::$GAME_STATUS_BLACK_TURN);
                }
            }
        }

        return new MoveResponse(
            200, Strings::$GAME_INFO_UPDATED);
    }

}