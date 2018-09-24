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
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Current Reality')
            )
            ->endpoint(
                ApiEndpoint::post('/shared/{id}', 'addSharedVision')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Shared Vision')
            )
            ->endpoint(
                ApiEndpoint::post('/initial/{id}', 'addInitialIntentions')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Initial Intentions')
            )
            ->endpoint(
                ApiEndpoint::get('/awe/{id}', 'getActions')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Get Systemic Map Items')
            )->endpoint(
                ApiEndpoint::get('/check/step/{id}', 'checkStep')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Check next unfilled step')
            )->endpoint(
                ApiEndpoint::get('/data/{id}', 'getProcessData')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Get Full Process Data')
            );
    }
}
