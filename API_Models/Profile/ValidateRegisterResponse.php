<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseResponse.php";

use API_Models\BaseResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Register response schema",
    properties: [
        new OA\Property(
            property: "UserName",
            description: <<<DESCRIPTION
                Имя пользователя 
                (не используется при авторзации, используется для отображения во время игры)
                DESCRIPTION,
            type: "string",
            example: "Василий Пупкин"
        ),
        new OA\Property(
            property: "Login",
            description: <<<DESCRIPTION
                Логин пользователя 
                (используется только при авторзации, 
                    не может внутри себя содержать пробелы)
                DESCRIPTION,
            type: "string",
            example: "Vasa"
        ),
        new OA\Property(
            property: "PasswordHash",
            description: <<<DESCRIPTION
                Хэш пароля пользователя 
                (Хэш пароля должен быть захеширован алгоритмом MD5, 
                    а затем из полученной строки вырезан последний символ)
                (В примере используется хеш пароля "123456")
                (Для проверки можно использовать сайт https://10015.io/tools/md5-encrypt-decrypt)
                DESCRIPTION,
            type: "string",
            example: "Эe10adc3949ba59abbe56e057f20f883"
        ),
        new OA\Property(
            property: "Telephone",
            description: <<<DESCRIPTION
                Номер телефона пользователя в международном формате
                (должен начинаться со знака + и не содержать пробелов) 
                DESCRIPTION,
            type: "string",
            example: "+71234567890"
        )
    ]
)]
class RegisterResponse extends BaseResponse
{
    public function __construct(bool $isWrongMethod)
    {
        parent::__construct($isWrongMethod);
    }
}