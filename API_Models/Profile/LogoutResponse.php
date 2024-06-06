<?php

namespace API_Models\Profile;

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Logout response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе деактивации ключа
                (если получен ответ, сообщение всегда говорит об успешной деактивации ключа)
                DESCRIPTION,
            type: "string",
            example: "Ключ удален"
        )
    ]
)]
class LogoutResponse extends BaseResponse
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