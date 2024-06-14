<?php

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Profile/RegisterRequest.php";
require_once "$currentFolder/../API_Models/Profile/RegisterResponse.php";
require_once "$currentFolder/../API_Models/Profile/ValidateRegisterRequest.php";
require_once "$currentFolder/../API_Models/Profile/ValidateRegisterResponse.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Profile/LoginRequest.php";
require_once "$currentFolder/../API_Models/Profile/LoginResponse.php";
require_once "$currentFolder/../API_Models/Profile/GetAuthorizeKeyStatusRequest.php";
require_once "$currentFolder/../API_Models/Profile/GetAuthorizeKeyStatusResponse.php";
require_once "$currentFolder/../API_Models/Profile/LogoutRequest.php";
require_once "$currentFolder/../API_Models/Profile/LogoutResponse.php";
require_once "$currentFolder/../API_Models/Profile/ProfileRequest.php";
require_once "$currentFolder/../API_Models/Profile/ProfileResponse.php";

$currentFolder = dirname(__FILE__);

require_once "$currentFolder/../API_Models/Profile/GetNameRequest.php";
require_once "$currentFolder/../API_Models/Profile/GetNameResponse.php";

$currentFolder = dirname(__FILE__);


use OpenApi\Attributes as OA;
use API_Models\Profile\RegisterRequest;
use API_Models\Profile\RegisterResponse;
use API_Models\Profile\ValidateRegisterRequest;
use API_Models\Profile\ValidateRegisterResponse;
use API_Models\Profile\LoginRequest;
use API_Models\Profile\LoginResponse;
use API_Models\Profile\GetAuthorizeKeyStatusRequest;
use API_Models\Profile\GetAuthorizeKeyStatusResponse;
use API_Models\Profile\LogoutRequest;
use API_Models\Profile\LogoutResponse;
use API_Models\Profile\ProfileRequest;
use API_Models\Profile\ProfileResponse;
use API_Models\Profile\GetNameRequest;
use API_Models\Profile\GetNameResponse;

$currentFolder = dirname(__FILE__); // swagger-php почему то теряет старое корректное значение переменной

require_once "$currentFolder/../Classes/Globals.php";
require_once "$currentFolder/../Classes/Strings.php";

use Classes\Globals;
use Classes\Strings;

#[OA\Tag(
    name: 'Profile',
    description: 'Операции связанные с профилем игрока/пользователя'
)]
class ProfileController
{
    #[OA\Post(
        path: '/api/profile/register',
        summary: "Регистрирует нового пользователя в системе",
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
            return new RegisterResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestCorrect()){
            return new RegisterResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();

        $isLoginAlreadyExists = $globals->getDatabase()->IsProfileExists($request->getLogin());

        if ($isLoginAlreadyExists){
            return new RegisterResponse(409, Strings::$LOGIN_ALREADY_EXISTS);
        }

        $globals->getDatabase()->CreateProfile(
            $request->getUserName(),
            $request->getLogin(),
            $request->getPasswordHash(),
            $request->getTelephone());

        return new RegisterResponse(200, Strings::$REGISTRATION_SUCCESSFUL);
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

    #[OA\Post(
        path: '/api/profile/login',
        summary: "Аутентификация пользователя и получение аутентификационного ключа",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Profile\LoginRequest::class
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
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"401",
                description: "Unauthorized - Неверный пароль/hash"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\LoginResponse::class
                )
            )
        ]
    )]
    public function Login(LoginRequest $request): LoginResponse{
        if ($request->getIsGetRequest()){
            return new LoginResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new LoginResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new LoginResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateCredentials($login, $request->getPasswordHash())){
            return new LoginResponse(401, Strings::$PASSWORD_WRONG);
        }

        $database->RemoveAuthorizeKeys($login);

        $authorizeKey = dechex(rand()).dechex(rand()).dechex(rand());
        $database->AddAuthorizeKey($login, $authorizeKey, $globals->getSettings()->AUTHORIZATION_KEY_VALID_HOURS);

        return new LoginResponse(200, Strings::$AUTHORIZATION_SUCCESSFUL, $authorizeKey);
    }

    #[OA\Get(
        path: '/api/profile/getAuthorizeKeyStatus',
        summary: "Получение статуса аутентификационного ключа",
        tags: ['Profile'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/GetAuthorizeKeyStatusRequest_LoginParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/GetAuthorizeKeyStatusRequest_KeyParameter"
            )
        ],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"401",
                description: "Unauthorized - Неверный/несуществующий Key"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\GetAuthorizeKeyStatusResponse::class
                )
            )
        ],
    )]
    public function GetAuthorizeKeyStatus(GetAuthorizeKeyStatusRequest $request): GetAuthorizeKeyStatusResponse{
        if ($request->getIsPostRequest()){
            return new GetAuthorizeKeyStatusResponse(404, Strings::$METHOD_POST_NOT_SUPPORTED, "");
        }

        if (!$request->IsRequestValid()){
            return new GetAuthorizeKeyStatusResponse(400, Strings::$WRONG_REQUEST, "");
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new GetAuthorizeKeyStatusResponse(406, Strings::$LOGIN_NOT_EXISTS, "");
        }

        $key = $request->getKey();

        if (!$database->ValidateAuthorizationKey($login, $key)){
            return new GetAuthorizeKeyStatusResponse(401, Strings::$KEY_WRONG, "");
        }

        $timeout = $database->GetAuthorizeKeyTimeout($login, $key);

        return new GetAuthorizeKeyStatusResponse(200, Strings::$KEY_VALID, $timeout);
    }

    #[OA\Post(
        path: '/api/profile/logout',
        summary: "Обнуление аутентификационного ключа пользователя",
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    ref: \API_Models\Profile\LogoutRequest::class
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
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"401",
                description: "Unauthorized - Неверный авторизационный ключ"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\LogoutResponse::class
                )
            )
        ]
    )]
    public function Logout(LogoutRequest $request): LogoutResponse{
        if ($request->getIsGetRequest()){
            return new LogoutResponse(404, Strings::$METHOD_GET_NOT_SUPPORTED);
        }

        if (!$request->IsRequestValid()){
            return new LogoutResponse(400, Strings::$WRONG_REQUEST);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new LogoutResponse(406, Strings::$LOGIN_NOT_EXISTS);
        }

        if (!$database->ValidateAuthorizationKey($login, $request->getKey())){
            return new LogoutResponse(401, Strings::$KEY_WRONG);
        }

        $database->RemoveAuthorizeKeys($login);

        return new LogoutResponse(200, Strings::$KEY_REMOVED);
    }

    #[OA\Get(
        path: '/api/profile/profile',
        summary: "Получение данных об авторизованном пользователе",
        tags: ['Profile'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/Profile_LoginParameter"
            ),
            new OA\QueryParameter(
                ref: "#/components/parameters/Profile_KeyParameter"
            )
        ],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"401",
                description: "Unauthorized - Неверный/несуществующий Key"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\ProfileResponse::class
                )
            )
        ],
    )]
    public function Profile(ProfileRequest $request): ProfileResponse{
        if ($request->getIsPostRequest()){
            return new ProfileResponse(404, null);
        }

        if (!$request->IsRequestValid()){
            return new ProfileResponse(400, null);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new ProfileResponse(406, null);
        }

        $key = $request->getKey();

        if (!$database->ValidateAuthorizationKey($login, $key)){
            return new ProfileResponse(401, null);
        }

        $profileInfo = $database->GetProfileInfo($login);

        return new ProfileResponse(200, $profileInfo);
    }

    #[OA\Get(
        path: '/api/profile/getName',
        summary: "Получение данных об авторизованном пользователе",
        tags: ['Profile'],
        parameters: [
            new OA\QueryParameter(
                ref: "#/components/parameters/GetName_LoginParameter"
            )
        ],
        responses: [
            new OA\Response(
                response:"404",
                description: "Wrong request method"
            ),
            new OA\Response(
                response:"406",
                description: "Not Acceptable - Неверный/несуществующий логин"
            ),
            new OA\Response(
                response:"400",
                description: <<<DESCRIPTION
                        Bad request - неправильно сформирован запрос
                    DESCRIPTION
            ),
            new OA\Response(
                response:"200",
                description:"OK",
                content: new OA\JsonContent(
                    ref: \API_Models\Profile\GetNameResponse::class
                )
            )
        ],
    )]
    public function GetName(GetNameRequest $request): GetNameResponse{
        if ($request->getIsPostRequest()){
            return new GetNameResponse(404, null);
        }

        if (!$request->IsRequestValid()){
            return new GetNameResponse(400, null);
        }

        $globals = new Globals();
        $login = $request->getLogin();
        $database = $globals->getDatabase();

        if (!$database->IsProfileExists($login)){
            return new GetNameResponse(406, null);
        }

        $profileInfo = $database->GetProfileInfo($login);

        return new GetNameResponse(200, $profileInfo);
    }

}