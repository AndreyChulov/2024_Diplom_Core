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

    public function __construct(
        bool $isWrongMethod, bool $isBadRequest = false, bool $isLoginAlreadyExists = false)
    {
        parent::__construct($isWrongMethod);

        if ($isBadRequest){
            parent::setStatusCode(400);

            return;
        }

        if ($isLoginAlreadyExists){
            parent::setStatusCode(409);

            return;
        }

        $this->_message = "Регистрация прошла успешно";
    }

    public function getMessage(): string
    {
        return $this->_message;
    }
}