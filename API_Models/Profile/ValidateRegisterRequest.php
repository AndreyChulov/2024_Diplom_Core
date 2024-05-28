<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/../BaseRequest.php";

use API_Models\BaseRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Register request schema",
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
class RegisterRequest extends BaseRequest
{
    private string $_userName;
    private string $_login;
    private string $_passwordHash;
    private string $_telephone;
    private bool $_isLoginCorrect;
    private bool $_isPasswordHashCorrect;
    private bool $_isTelephoneCorrect;
    private bool $_isRequestCorrect;

    public function getUserName(): string
    {
        return $this->_userName;
    }

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function getPasswordHash(): string
    {
        return $this->_passwordHash;
    }

    public function getTelephone(): string
    {
        return $this->_telephone;
    }

    public function __construct()
    {
        parent::__construct();

        $this->_userName = $_POST['UserName'];
        $this->_login = $_POST['Login'];
        $this->_passwordHash = $_POST['PasswordHash'];
        $this->_telephone = $_POST['Telephone'];
    }

    public function IsLoginCorrect():bool{
        if (!isset($this->_isLoginCorrect)){
            $this->_isLoginCorrect = !str_contains($this->_login, " ");
        }

        return $this->_isLoginCorrect;
    }

    public function IsPasswordHashCorrect():bool{
        if (!isset($this->_isPasswordHashCorrect)){
            $this->_isPasswordHashCorrect =
                strlen($this->_passwordHash) == 32 and !str_contains($this->_passwordHash, " ");
        }

        return $this->_isPasswordHashCorrect;
    }

    public function IsTelephoneCorrect():bool{
        if (!isset($this->_isTelephoneCorrect)){
            $this->_isTelephoneCorrect =
                str_starts_with($this->_telephone, "+") and !str_contains($this->_telephone, " ");
        }

        return $this->_isTelephoneCorrect;
    }


    public function IsRequestCorrect():bool{
        if (!isset($this->_isRequestCorrect)){
            $this->_isRequestCorrect =
                $this->IsLoginCorrect() and
                $this->IsPasswordHashCorrect() and
                $this->IsTelephoneCorrect();
        }

        return $this->_isRequestCorrect;
    }
}