<?php

namespace API_Models\Game;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../BaseRequest.php";
require_once "$currentFolder/../../Classes/Game/ChessAddressTranslator.php";

use API_Models\BaseRequest;
use Classes\Game\ChessAddressTranslator;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Game move request schema",
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
        ),
        new OA\Property(
            property: "Move",
            description: <<<DESCRIPTION
                    Ход для пешки. <br>
                    Формат хода: <br>
                    [Address][-|*][Address] <br>
                    Где: <br>
                    Address - позиция в шахматной нотации (слева откуда ходим, справа куда ходим) <br>
                    - простой ход <br>
                    * ход с взятием пешки противника
                DESCRIPTION,
            type: "string",
            example: "F6-E7"
        )
    ]
)]
class MoveRequest extends BaseRequest
{
    private string $_login;
    private string $_key;
    private string $_gameKey;
    private string $_move;

    public function getMove(): string
    {
        return $this->_move;
    }

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
        $this->_move = $_POST['Move'] ?? "";
    }

    public function IsRequestValid(): bool{
        return $this->_login !== "" && $this->_key !== "" && $this->_gameKey !== "" &&
            strlen($this->_move) === 5 &&
            ChessAddressTranslator::IsChessAddressValid(substr($this->_move, 0, 2)) &&
            ChessAddressTranslator::IsChessAddressValid(substr($this->_move, 3)) &&
            ($this->_move[2] === "-" || $this->_move[2] === "*");
    }
}