<?php

namespace API_Models\Server;

require_once dirname(__FILE__).'/../BaseResponse.php';

use API_Models\BaseResponse;

class InstallResponse extends BaseResponse
{
    public function __construct(bool $isWrongMethod)
    {
        parent::__construct($isWrongMethod);
    }
}