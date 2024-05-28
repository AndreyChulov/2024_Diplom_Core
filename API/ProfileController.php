<?php

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Profile/RegisterRequest.php";
require_once "$currentFolder/../API_Models/Profile/RegisterResponse.php";
require_once "$currentFolder/../API_Models/Profile/ValidateRegisterRequest.php";
require_once "$currentFolder/../API_Models/Profile/ValidateRegisterResponse.php";

use OpenApi\Attributes as OA;
use API_Models\Profile\RegisterRequest;
use API_Models\Profile\RegisterResponse;
use API_Models\Profile\ValidateRegisterRequest;
use API_Models\Profile\ValidateRegisterResponse;

#[OA\Tag(
    name: 'Profile',
    description: 'Операции связанные с профилем игрока/пользователя'
)]
class ProfileController
{
    #[OA\Post(
        path: '/api/profile/register',
        summary: "Регистрирует нового пользователя в системе [В разработке]",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Profile\RegisterRequest::class
                )
            )
        ),
        tags: ['Profile'],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request, неправильно сформирован запрос, 
                        вызовите /api/profile/validateRegister для получения деталей по ошибке
                    DESCRIPTION
            ),
            new OA\Response(
                response:"409",
                description: <<<DESCRIPTION
                        Conflict, запрошенный Login пользователя уже занят
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\RegisterResponse::class
                )
            )
        ]
    )]
    public function Register(RegisterRequest $request): RegisterResponse
    {
        if ($request->getIsGetRequest()){
            return new RegisterResponse(true);
        }

        if (!$request->IsRequestCorrect()){
            return new RegisterResponse(false, true);
        }

        $isLoginAlreadyExists = false;

        return new RegisterResponse(false, false, $isLoginAlreadyExists);
    }

    #[OA\Post(
        path: '/api/profile/validateRegister',
        summary: <<<SUMMARY
            Проверяет правильность запроса о регистрации и 
                выдает описание ошибки, в случае ее наличия в запросе
            SUMMARY,
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Profile\ValidateRegisterRequest::class
                )
            )
        ),
        tags: ['Profile'],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\ValidateRegisterResponse::class
                )
            )
        ]
    )]
    public function ValidateRegister(ValidateRegisterRequest $request): ValidateRegisterResponse
    {
        if ($request->getIsGetRequest()){
            return new ValidateRegisterResponse(true, $request);
        }


        return new ValidateRegisterResponse(false, $request);
    }
}