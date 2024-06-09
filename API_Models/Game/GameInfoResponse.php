<?php

namespace API_Models\Game;

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Get authorizeKey status response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе авторизации
                (если получен ответ, сообщение всегда говорит об активности ключа)
                DESCRIPTION,
            type: "string",
            example: "Ключ активен"
        ),
        new OA\Property(
            property: "Timeout",
            description: <<<DESCRIPTION
                Время до "протухания" ключа
                DESCRIPTION,
            type: "string",
            example: "00:00:00"
        )
    ]
)]
class StatusResponse extends BaseResponse
{
    private string $_message;
    private string $_timeout;

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function getTimeout(): string
    {
        return $this->_timeout;
    }

    public function __construct(int $statusCode, string $message, string $timeout)
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_timeout = $timeout;
    }
}