<?php

namespace API_Models\Server;

use JsonSerializable;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Base response schema",
    properties: [
        new OA\Property(
            property: "StatusCode",
            description: "Код ответа от сервера",
            type: "int",
            example: 200
        )
    ]
)]
class BaseResponse implements JsonSerializable
{

    private int $_statusCode;

    public function __construct(){
        $this->_statusCode = 200;
    }

    public function getStatusCode(): int
    {
        return $this->_statusCode;
    }

    protected function setStatusCode(int $statusCode): void{
        $this->_statusCode = $statusCode;
    }

    public function jsonSerialize(): array{
        $className = get_class($this);
        $classMethods = get_class_methods($className);
        $gettableProperties = Array();

        foreach ($classMethods as $method){
            if (str_starts_with($method, 'get')){
                $jsonProperty = substr($method, 3);
                $gettableProperties[$jsonProperty] = $this->$method();
            }
        }

        return $gettableProperties;
    }
}