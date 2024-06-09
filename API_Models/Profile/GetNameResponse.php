<?php

namespace API_Models\Profile;

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../BaseResponse.php";
require_once "$currentFolder/../../DataModels/ProfileDataModel.php";

use API_Models\BaseResponse;
use DataModels\ProfileDataModel;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Profile response schema",
    properties: [
        new OA\Property(
            property: "Name",
            description: <<<DESCRIPTION
                Имя пользователя 
                DESCRIPTION,
            type: "string",
            example: "Василий Пупкин"
        ),
        new OA\Property(
            property: "Login",
            description: <<<DESCRIPTION
                Логин пользователя 
                DESCRIPTION,
            type: "string",
            example: "Vasa"
        ),
        new OA\Property(
            property: "Phone",
            description: <<<DESCRIPTION
                Номер телефона пользователя
                DESCRIPTION,
            type: "string",
            example: "+71234567890"
        )
    ]
)]
class ProfileResponse extends BaseResponse
{
    private string $_name;
    private string $_login;
    private string $_phone;

    public function getName(): string
    {
        return $this->_name;
    }

    public function getLogin(): string
    {
        return $this->_login;
    }

    public function getPhone(): string
    {
        return $this->_phone;
    }

    public function __construct(int $statusCode, ProfileDataModel|null $profileInfo)
    {
        parent::__construct(false);

        parent::setStatusCode($statusCode);

        if (!isset($profileInfo)){
            $profileInfo = new ProfileDataModel();
        }

        $this->_login = $profileInfo->getLogin();
        $this->_phone = $profileInfo->getPhone();
        $this->_name = $profileInfo->getName();
    }
}