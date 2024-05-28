<?php

namespace API_Models\Profile;

$currentFolder = dirname(__FILE__);
require_once "$currentFolder/../BaseResponse.php";
require_once "$currentFolder/ValidateRegisterRequest.php";

use API_Models\BaseResponse;
use API_Models\Profile\ValidateRegisterRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Validate register response schema",
    properties: [
        new OA\Property(
            property: "IsRequestOk",
            description: <<<DESCRIPTION
                Флаг, показывающий что запрос сформирован верно
                DESCRIPTION,
            type: "bool",
            example: true
        ),
        new OA\Property(
            property: "RequestException",
            description: <<<DESCRIPTION
                Описание ошибки запроса (что неверно установлено)
                DESCRIPTION,
            type: "string",
            example: "Поле Telephone задано неверно"
        )
    ]
)]
class ValidateRegisterResponse extends BaseResponse
{
    private bool $_isRequestOk;
    private string $_requestException;

    public function getIsRequestOk(): bool
    {
        return $this->_isRequestOk;
    }

    public function getRequestException(): string
    {
        return $this->_requestException;
    }

    public function __construct(bool $isWrongMethod, ValidateRegisterRequest $request)
    {
        parent::__construct($isWrongMethod);

        if ($request->IsRequestCorrect()){
            $this->_isRequestOk = true;
            $this->_requestException = "";

            return;
        }

        $this->_isRequestOk = false;

        if (!$request->IsTelephoneCorrect()){
            $this->_requestException = "Поле Telephone задано неверно";
        } elseif (!$request->IsLoginCorrect()){
            $this->_requestException = "Поле Login задано неверно";
        } elseif (!$request->IsPasswordHashCorrect()){
            $this->_requestException = "Поле PasswordHash задано неверно";
        }
    }
}