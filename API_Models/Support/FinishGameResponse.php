<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use Classes\Strings;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Game start response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе созданной игры
                (если получен ответ, сообщение говорит об ожидании 2го игрока либо о начале игры)
                DESCRIPTION,
            type: "string",
            example: "Ожидание второго игрока"
        ),
        new OA\Property(
            property: "GameKey",
            description: <<<DESCRIPTION
                Ключ начатой игры
                (Необходим для доступа к игре)
                DESCRIPTION,
            type: "string",
            example: "72e94b3312ed163703f666"
        )
    ]
)]
class StartResponse extends BaseResponse
{
    private string $_message;
    private string $_gameKey;

    public function getGameKey(): string
    {
        return $this->_gameKey;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(int $statusCode, string $message, string $gameKey = "")
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_gameKey = $gameKey;
    }
}