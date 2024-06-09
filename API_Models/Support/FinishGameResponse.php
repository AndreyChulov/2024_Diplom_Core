<?php

namespace API_Models\Support;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use Classes\Strings;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Finish game response schema",
    properties: [
        new OA\Property(
            property: "Message",
            description: <<<DESCRIPTION
                Сообщение о статусе удаления игры
                (если получен ответ, сообщение всегда говорит об успешности процесса)
                DESCRIPTION,
            type: "string",
            example: "Игра принудительно завершена"
        )
    ]
)]
class FinishGameResponse extends BaseResponse
{
    private string $_message;

    public function getMessage(): string
    {
        return $this->_message;
    }

    public function __construct(int $statusCode, string $message)
    {
        parent::__construct(false);
        parent::setStatusCode($statusCode);

        $this->_message = $message;
    }
}