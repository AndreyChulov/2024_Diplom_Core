<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Register response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе регистрации
                (если получен ответ, сообщение всегда говорит об успешной регистрации)
                DESCRIPTION,
            type: "string",
            example: "Регистрация прошла успешно"
        )
    ]
)]
class RegisterResponse extends BaseResponse
{
    private string $_message;

    public function __construct(int $statusCode, string $message)
    {
        parent::__construct(false);

        parent::setStatusCode($statusCode);
        $this->_message = $message;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }
}