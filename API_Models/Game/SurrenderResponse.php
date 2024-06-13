<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Game surrender response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе выполнения команды
                (если получен ответ, сообщение всегда говорит об обновлении данных игры)
                DESCRIPTION,
            type: "string",
            example: "Данные игры обновлены"
        )
    ]
)]
class SurrenderResponse extends BaseResponse
{
    private string $_message;

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(int $statusCode, string $message)
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
    }
}