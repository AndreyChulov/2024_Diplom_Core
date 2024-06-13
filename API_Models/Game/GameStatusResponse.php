<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Get gameStatus response schema",
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
            property: "GameStatus",
            description: <<<DESCRIPTION
                Статус игры
                DESCRIPTION,
            type: "string",
            example: "Ожидание второго игрока"
        )
    ]
)]
class GameStatusResponse extends BaseResponse
{
    private string $_message;
    private string $_gameStatus;

    public function getGameStatus(): string
    {
        return $this->_gameStatus;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(
        int $statusCode, string $message, string $gameStatus = "")
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_gameStatus = $gameStatus;
    }
}