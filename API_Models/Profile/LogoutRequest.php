<?php

namespace API_Models\Profile;

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Logout request schema",
    properties: [
        new OA\Property(
            property: "Login",
            description: <<<DESCRIPTION
                Логин пользователя
                DESCRIPTION,
            type: "string",
            example: "Vasa"
        ),
        new OA\Property(
            property: "Key",
            description: <<<DESCRIPTION
                Авторизационный ключ, для деактивации
                DESCRIPTION,
            type: "string",
            example: "4472a5cc389cb36065b5a336"
        )
    ]
)]
class LogoutRequest extends BaseRequest
{
    private string $_login;

    private string $_key;

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function getKey(): string
    {
        return $this->_key;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_login = $_POST['Login'] ?? "";
        $this->_key = $_POST['Key'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_key !== "";
    }
}