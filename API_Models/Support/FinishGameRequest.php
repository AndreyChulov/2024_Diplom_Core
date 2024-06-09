<?php

namespace API_Models\Support;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Finish game request schema",
    properties: [
        new OA\Property(
            property: "Login",
            description: <<<DESCRIPTION
                Логин пользователя
                DESCRIPTION,
            type: "string",
            example: "Vasa"
        )
    ]
)]
class FinishGameRequest extends BaseRequest
{
    private string $_login;

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_login = $_POST['Login'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "";
    }
}