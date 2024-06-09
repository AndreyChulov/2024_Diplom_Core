<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\QueryParameter(
    parameter: "Profile_LoginParameter",
    name: "Login",
    description: <<<DESCRIPTION
                Логин пользователя
                DESCRIPTION,
    required: true,
    example: "Vasa"
)]
#[OA\QueryParameter(
    parameter: "Profile_KeyParameter",
    name: "Key",
    description: <<<DESCRIPTION
                Ключ авторизации для проверки
                DESCRIPTION,
    required: true,
    example: "4472a5cc389cb36065b5a336"
)]
class ProfileRequest extends BaseRequest
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

        $this->_login = $_GET['Login'] ?? "";
        $this->_key = $_GET['Key'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_key !== "";
    }
}