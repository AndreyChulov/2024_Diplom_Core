<?php

namespace API_Models\Profile;

require_once dirname(__FILE__)."/RegisterRequest.php";

use API_Models\Profile\RegisterRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Validate register request schema"
)]
class ValidateRegisterRequest extends RegisterRequest
{
    public function __construct()
    {
        parent::__construct();
    }
}