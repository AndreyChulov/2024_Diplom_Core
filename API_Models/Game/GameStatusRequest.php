<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\QueryParameter(
    parameter: "GameStatus_GameKeyParameter",
    name: "GameKey",
    description: <<<DESCRIPTION
                Ключ игры
                DESCRIPTION,
    required: true,
    example: "1ee106de7b06f5a16da1632b"
)]
class GameStatusRequest extends BaseRequest
{
    private string $_gameKey;

    public function getGameKey(): string
    {
        return $this->_gameKey;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_gameKey = $_GET['GameKey'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_gameKey !== "";
    }
}