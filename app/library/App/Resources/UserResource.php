<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\User;
use App\Transformers\UserTransformer;
use App\Controllers\UserController;
use App\Constants\AclRoles;

class UserResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('User')
            ->model(User::class)
            ->expectsJsonData()
            ->transformer(UserTransformer::class)
            ->handler(UserController::class)
            ->itemKey('user')
            ->collectionKey('users')
            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
//            ->endpoint(ApiEndpoint::all()
//                ->allow(AclRoles::USER)
//                ->description('Returns all registered users')
//            )
            ->endpoint(
                ApiEndpoint::get('/', 'getUsers')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::get('/me', 'me')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/authenticate', 'authenticate')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description('Authenticates user credentials provided in the authorization header and returns an access token')
                    ->exampleResponse([
                        'token' => 'co126bbm40wqp41i3bo7pj1gfsvt9lp6',
                        'expires' => 1451139067
                    ])
            )
            ->endpoint(
                ApiEndpoint::post('/createManagerPublic', 'createManagerPublic')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/createUserPublic', 'createUserPublic')
                    ->allow(AclRoles::UNAUTHORIZED)
                    ->deny(AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/createManager', 'createManager')
                    ->allow(AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/createUser', 'createUser')
                    ->allow(AclRoles::MANAGER, AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/create-profile', 'createProfile')
                    ->allow(AclRoles::MANAGER, AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::post('/setProcessPermissions', 'setProcessPermissions')
                    ->allow(AclRoles::MANAGER, AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Returns the currently logged in user')
            )
            ->endpoint(
                ApiEndpoint::get('/getProcessPermissions/{id}', 'getProcessPermissions')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('getProcessPermissions')
            )
            ->endpoint(
                ApiEndpoint::put('/updateOtherUser/{id}', 'updateOtherUser')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('updateOtherUser')
            )
            //deactivateOtherUser
            ->endpoint(
                ApiEndpoint::delete('/deactivateOtherUser/{id}', 'deactivateOtherUser')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('deactivateOtherUser')
            )
            ->endpoint(
                ApiEndpoint::put('/updateUser', 'updateUser')
                    ->allow(AclRoles::MANAGER, AclRoles::AUTHORIZED)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::UNAUTHORIZED)
                    ->description('update user')
            );
    }
}
