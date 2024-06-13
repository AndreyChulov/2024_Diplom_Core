<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Move response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение об успешном обновлении данных
                DESCRIPTION,
            type: "string",
            example: "Данные игры обновлены"
        )
    ]
)]
class MoveResponse extends BaseResponse
{
    private string $_message;

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(
        int $statusCode, string $message)
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
    }
}