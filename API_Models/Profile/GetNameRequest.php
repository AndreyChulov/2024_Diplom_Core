<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\QueryParameter(
    parameter: "GetName_LoginParameter",
    name: "Login",
    description: <<<DESCRIPTION
                Логин пользователя
                DESCRIPTION,
    required: true,
    example: "Vasa"
)]
class GetNameRequest extends BaseRequest
{
    private string $_login;

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_login = $_GET['Login'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "";
    }
}