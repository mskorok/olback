<?php

namespace App\Resources;

use App\Constants\AclRoles;
use App\Controllers\ProcessController;
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
                    ->description('Get Actions for Awesomplete')
            )->endpoint(
                ApiEndpoint::get('/pis/{id}', 'createPIS')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Create PIS')
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
            )->endpoint(
                ApiEndpoint::post('/subscription/{id}', 'setSubscription')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Set Subscription')
            )->endpoint(
                ApiEndpoint::get('/subscription', 'getSubscription')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Get Subscription')
            )->endpoint(
                ApiEndpoint::get('/subscription/unset', 'unsetSubscription')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Unset Subscription')
            );
    }
}
