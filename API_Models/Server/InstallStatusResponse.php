<?php

namespace API_Models\Server;

$currentFolder = dirname(__FILE__);
require_once "$currentFolder/../BaseResponse.php";
require_once "$currentFolder/../../Classes/Status.php";

use API_Models\BaseResponse;
use Classes\Status;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "InstallStatus response schema",
    properties: [
        new OA\Property(
            property: "IsSqlLite3ClassLoaded",
            description: "Флаг, был ли загружен/включен модуль для работы с SqlLite3",
            type: "bool",
            example: true
        ),
        new OA\Property(
            property: "IsDatabaseExists",
            description: "Флаг, означающий, присутствует ли база данных на сервере",
            type: "bool",
            example: false
        )
    ]
)]
class InstallStatusResponse extends BaseResponse
{
    private bool $_isDatabaseExists = false;
    private bool $_isSqlLite3ClassLoaded = false;

    public function getIsSqlLite3ClassLoaded(): bool
    {
        return $this->_isSqlLite3ClassLoaded;
    }

    public function getIsDatabaseExists(): bool
    {
        return $this->_isDatabaseExists;
    }


    public function __construct(bool $isWrongMethod, Status $status = null)
    {
        parent::__construct($isWrongMethod);

        if ($isWrongMethod) {
            return;
        }

        $this->_isDatabaseExists = $status->IsDatabaseExists();
        $this->_isSqlLite3ClassLoaded = $status->IsSqlLite3ClassLoaded();
    }
}