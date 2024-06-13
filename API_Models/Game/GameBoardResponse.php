<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Get gameBoard response schema",
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
            property: "GameBoard",
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
        )
    ]
)]
class GameBoardResponse extends BaseResponse
{
    private string $_message;
    private string $_gameBoard;

    public function getGameBoard(): string
    {
        return $this->_gameBoard;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(
        int $statusCode, string $message, string $movies = "")
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_gameBoard = $movies;
    }
}