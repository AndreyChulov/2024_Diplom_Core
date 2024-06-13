<?php

namespace API_Models\Game;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Checker acceptable movies response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение об успешном получении данных
                DESCRIPTION,
            type: "string",
            example: "Данные игры получены"
        ),
        new OA\Property(
            property: "Movies",
            description: <<<DESCRIPTION
                    Массив доступных ходов для пешки. <br>
                    Формат хода: <br>
                    [Address][-|*][Address] <br>
                    Где: <br>
                    Address - позиция в шахматной нотации (слева откуда ходим, справа куда ходим) <br>
                    - простой ход <br>
                    * ход с взятием пешки противника
                DESCRIPTION,
            type: "string",
            example:
            "[\"F6-E7\", \"F6*D4\"]"
        )
    ]
)]
class CheckerAcceptableMoviesResponse extends BaseResponse
{
    private string $_message;
    private string $_movies;

    public function getMovies(): string
    {
        return $this->_movies;
    }

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(
        int $statusCode, string $message, string $movies = "")
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
        $this->_movies = $movies;
    }
}