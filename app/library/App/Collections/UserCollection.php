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
