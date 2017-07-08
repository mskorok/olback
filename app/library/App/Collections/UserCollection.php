<?php

namespace App\Collections;

use App\Controllers\UserController;
use PhalconRest\Api\ApiCollection;
use PhalconRest\Api\ApiEndpoint;

class UserCollection extends ApiCollection
{
    protected function initialize()
    {
        $this
            ->name('User')
            ->handler(UserController::class)

            ->endpoint(ApiEndpoint::get('/createManager', 'createManager'))
            // ->endpoint(ApiEndpoint::get('/postman.json', 'postman'));
    }
}
// namespace App\Collections;
//
// use App\Controllers\HelperController;
// use PhalconRest\Api\Collection;
// use PhalconRest\Api\Endpoint;
// use App\Constants\AclRoles;
//
// class HelperCollection extends Collection {
//
//     protected function initialize() {
//         $this
//             ->name('HelperServices')
//             ->handler(HelperController::class)
//             ->endpoint(Endpoint::get('/tags', 'getTags')
//                 ->name('getTags')
//                 ->allow(AclRoles::BIUSER,AclRoles::MESSENGER_MANAGER)
//             )->endpoint(Endpoint::get('/checktags/{tags}', 'checkTags')
//                 ->name('checkTags')
//                 ->allow(AclRoles::BIUSER,AclRoles::MESSENGER_MANAGER)
//             )
//             ->endpoint(Endpoint::get('/nationality', 'getNationality')
//                 ->name('getNationality')
//                 ->allow(AclRoles::BIUSER,AclRoles::MESSENGER_MANAGER)
//             )->endpoint(Endpoint::get('/customers/find/search', 'searchCustomers')
//                 ->name('searchCustomers')
//                 ->allow(AclRoles::BIUSER,AclRoles::MESSENGER_MANAGER)
//             );
//     }
//
// }
