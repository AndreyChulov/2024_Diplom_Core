<?php

namespace API_Models\Game;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../BaseRequest.php";
require_once "$currentFolder/../../Classes/Game/ChessAddressTranslator.php";

use API_Models\BaseRequest;
use Classes\Game\ChessAddressTranslator;
use OpenApi\Attributes as OA;

#[OA\QueryParameter(
    parameter: "AcceptableMovies_LoginParameter",
    name: "Login",
    description: <<<DESCRIPTION
                Логин пользователя
                DESCRIPTION,
    required: true,
    example: "Vasa"
)]
#[OA\QueryParameter(
    parameter: "AcceptableMovies_KeyParameter",
    name: "Key",
    description: <<<DESCRIPTION
                Ключ авторизации
                DESCRIPTION,
    required: true,
    example: "4472a5cc389cb36065b5a336"
)]
#[OA\QueryParameter(
    parameter: "AcceptableMovies_GameKeyParameter",
    name: "GameKey",
    description: <<<DESCRIPTION
                Ключ игры
                DESCRIPTION,
    required: true,
    example: "1ee106de7b06f5a16da1632b"
)]
class AcceptableMoviesRequest extends BaseRequest
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

        $this->_login = $_GET['Login'] ?? "";
        $this->_key = $_GET['Key'] ?? "";
        $this->_gameKey = $_GET['GameKey'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_key !== "" && $this->_gameKey !== "";
    }
}