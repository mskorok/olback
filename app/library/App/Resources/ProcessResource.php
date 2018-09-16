<?php

namespace App\Resources;

use App\Constants\AclRoles;
use App\Controllers\ProcessController;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Model\Process;
use App\Transformers\ProcessTransformer;

class ProcessResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Process')
            ->model(Process::class)
            ->expectsJsonData()
            ->transformer(ProcessTransformer::class)
            ->itemKey('process')
            ->collectionKey('process')
            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(ProcessController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create())
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update())
            ->endpoint(ApiEndpoint::remove())
            ->endpoint(
                ApiEndpoint::post('/current/{id}', 'addCurrentReality')
                    ->allow(AclRoles::MANAGER)
                    ->allow(AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Current Reality')
            )
            ->endpoint(
                ApiEndpoint::post('/shared/{id}', 'addSharedVision')
                    ->allow(AclRoles::MANAGER)
                    ->allow(AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Shared Vision')
            )
            ->endpoint(
                ApiEndpoint::post('/initial/{id}', 'addInitialIntentions')
                    ->allow(AclRoles::MANAGER)
                    ->allow(AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Initial Intentions')
            )
            ->endpoint(
                ApiEndpoint::get('/awe/{id}', 'getActions')
                    ->allow(AclRoles::MANAGER)
                    ->allow(AclRoles::ADMINISTRATOR)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic MapI tems')
            );
    }
}
