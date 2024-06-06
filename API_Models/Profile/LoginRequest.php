<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Login request schema",
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
            property: "PasswordHash",
            description: <<<DESCRIPTION
                Хэш пароля пользователя 
                (Хэш пароля должен быть захеширован алгоритмом MD5, 
                    а затем из полученной строки вырезан последний символ)
                (В примере используется хеш пароля "123456")
                (Для проверки можно использовать сайт https://10015.io/tools/md5-encrypt-decrypt)
                DESCRIPTION,
            type: "string",
            example: "e10adc3949ba59abbe56e057f20f883"
        )
    ]
)]
class LoginRequest extends BaseRequest
{
    private string $_login;
    private string $_passwordHash;

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function getPasswordHash(): string
    {
        return $this->_passwordHash;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_login = $_POST['Login'] ?? "";
        $this->_passwordHash = $_POST['PasswordHash'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_passwordHash !== "";
    }
}