<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Game surrender request schema",
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
                Авторизационный ключ
                DESCRIPTION,
            type: "string",
            example: "4472a5cc389cb36065b5a336"
        ),
        new OA\Property(
            property: "GameKey",
            description: <<<DESCRIPTION
                Ключ игры
                DESCRIPTION,
            type: "string",
            example: "1ee106de7b06f5a16da1632b"
        )
    ]
)]
class SurrenderRequest extends BaseRequest
{
    private string $_login;
    private string $_key;
    private string $_gameKey;

    public function getGameKey(): string
    {
        return $this->_gameKey;
    }

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
        $this->_gameKey = $_POST['GameKey'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_key !== "" && $this->_gameKey !== "";
    }
}