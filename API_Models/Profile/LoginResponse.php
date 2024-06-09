<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Login response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе авторизации
                (если получен ответ, сообщение всегда говорит об успешной авторизации)
                DESCRIPTION,
            type: "string",
            example: "Авторизация прошла успешно"
        ),
        new OA\Property(
            property: "AuthorizeKey",
            description: <<<DESCRIPTION
                Авторизационный ключ, сервер не инициализирует сессию 
                для авторизации, вместо этого он выдает ключ на несколько 
                часов, по прошествии времени ключ "протухает" и требуется
                снова авторизоваться на сервере, он выдаст другой ключ
                DESCRIPTION,
            type: "string",
            example: "4472a5cc389cb36065b5a336"
        )
    ]
)]
class LoginResponse extends BaseResponse
{
    private string $_message;
    private string $_authorizeKey;

    public function getAuthorizeKey(): string
    {
        return $this->_authorizeKey;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(int $statusCode, string $message, string $authorizeKey = "")
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_authorizeKey  = $authorizeKey;
    }
}