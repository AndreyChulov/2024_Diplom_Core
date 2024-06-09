<?php

namespace API_Models\Game;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../BaseRequest.php";
require_once "$currentFolder/../../DataModels/GameInfoDataModel.php";

use API_Models\BaseResponse;
use DataModels\GameInfoDataModel;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Get gameInfo response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение об успешном получении данных
                DESCRIPTION,
            type: "string",
            example: "Данные игры получены"
        ),
        new OA\Property(
            property: "WhitePlayerLogin",
            description: <<<DESCRIPTION
                Логин игрока играющего за белых
                DESCRIPTION,
            type: "string",
            example: "Vasa"
        ),
        new OA\Property(
            property: "BlackPlayerLogin",
            description: <<<DESCRIPTION
                Логин игрока играющего за черных, или пустая строка, если игрок не подключен к игре
                DESCRIPTION,
            type: "string",
            example: ""
        ),
        new OA\Property(
            property: "GameKey",
            description: <<<DESCRIPTION
                Ключ игры
                DESCRIPTION,
            type: "string",
            example: "23eb06c7734b856e49ce3c15"
        ),
        new OA\Property(
            property: "GameStatus",
            description: <<<DESCRIPTION
                Статус игры
                DESCRIPTION,
            type: "string",
            example: "Ожидание второго игрока"
        ),
        new OA\Property(
            property: "Board",
            description: <<<DESCRIPTION
                Доска в виде двумерного массива 8х8. <br>
                Каждый элемент массива может быть следующим: <br>
                null - пустая клетка поля <br>
                W - белая обычная шашка <br>
                WR - белая дамка <br>
                B - черная обычная шашка <br>
                BR - черная дамка
                DESCRIPTION,
            type: "string",
            example: 
                "[".
                    "[\"W\",null,\"W\",null,\"W\",null,\"W\",null],".
                    "[null,\"W\",null,\"W\",null,\"W\",null,\"W\"],".
                    "[\"W\",null,\"W\",null,\"W\",null,\"W\",null],".
                    "[null,null,null,null,null,null,null,null],".
                    "[null,null,null,null,null,null,null,null],".
                    "[null,\"B\",null,\"B\",null,\"B\",null,\"B\"],".
                    "[\"B\",null,\"B\",null,\"B\",null,\"B\",null],".
                    "[null,\"B\",null,\"B\",null,\"B\",null,\"B\"]".
                "]"
        ),
        new OA\Property(
            property: "GameCreated",
            description: <<<DESCRIPTION
                Дата и время создания игры (время серверное)
                DESCRIPTION,
            type: "string",
            example: "2024-06-09 03:46:39"
        )
    ]
)]
class GameInfoResponse extends BaseResponse
{
    private string $_message;
    private string $_whitePlayerLogin;
    private string $_blackPlayerLogin;
    private string $_gameKey;
    private string $_gameStatus;
    private string $_board;
    private string $_gameCreated;

    public function getWhitePlayerLogin(): string
    {
        return $this->_whitePlayerLogin;
    }

    public function getBlackPlayerLogin(): string
    {
        return $this->_blackPlayerLogin;
    }

    public function getGameKey(): string
    {
        return $this->_gameKey;
    }

    public function getGameStatus(): string
    {
        return $this->_gameStatus;
    }

    public function getBoard(): string
    {
        return $this->_board;
    }

    public function getGameCreated(): string
    {
        return $this->_gameCreated;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(
        int $statusCode, string $message,
        GameInfoDataModel $gameInfoDataModel = new GameInfoDataModel())
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_whitePlayerLogin = $gameInfoDataModel->getWhitePlayerLogin();
        $this->_blackPlayerLogin = $gameInfoDataModel->getBlackPlayerLogin();
        $this->_gameKey = $gameInfoDataModel->getGameKey();
        $this->_gameStatus = $gameInfoDataModel->getGameStatus();
        $this->_board = $gameInfoDataModel->getBoard();
        $this->_gameCreated = $gameInfoDataModel->getGameCreated();
    }
}