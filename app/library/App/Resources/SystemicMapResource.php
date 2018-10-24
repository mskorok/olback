<?php

namespace App\Resources;

use App\Transformers\SystemicMapTransformer;
use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\SystemicMap;
use App\Controllers\SystemicMapController;
use App\Constants\AclRoles;

class SystemicMapResource extends ApiResource
{
    public function initialize()
    {
        $this
            ->name('SystemicMap')
            ->model(SystemicMap::class)
            ->expectsJsonData()
             ->transformer(SystemicMapTransformer::class)
            ->handler(SystemicMapController::class)
            ->itemKey('systemicmap')
            ->collectionKey('systemicmap')
            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
            //Map
            ->endpoint(
                ApiEndpoint::get('/getAll', 'getSystemicMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Maps')
            )
            ->endpoint(
                ApiEndpoint::get('/getSystemic/{id}', 'getSystemicMapByProcess')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Maps by Process')
            )->endpoint(
                ApiEndpoint::post('/create', 'createSystemicMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Systemic Map')
            )->endpoint(
                ApiEndpoint::put('/{id}', 'updateSystemicMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Update Systemic Map')
            )->endpoint(
                ApiEndpoint::delete('/{id}', 'deleteSystemicMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Systemic Map')
            )
            //Items
            ->endpoint(
                ApiEndpoint::get('/getItem/{id}', 'getSystemicItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Map Items')
            )->endpoint(
                ApiEndpoint::post('/createItem', 'createSystemicMapItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Systemic Map Item')
            )->endpoint(
                ApiEndpoint::put('/item/{id}', 'updateSystemicItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Update Systemic Map Item')
            )->endpoint(
                ApiEndpoint::delete('/item/{id}', 'deleteSystemicItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Systemic Map Item')
            )
            //Chain
            ->endpoint(
                ApiEndpoint::get('/getChain/{id}/{type}', 'getChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('getChain')
            )
            ->endpoint(
                ApiEndpoint::delete('/deleteChain/{id}', 'deleteChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Chain')
            )
            ->endpoint(
                ApiEndpoint::post('/createChain', 'createChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Chain')
            )
            //Tree
            ->endpoint(
                ApiEndpoint::get('/getItemTree/{id}', 'getSystemicItemTree')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Map Items Tree')
            )
            //Action List
            ->endpoint(
                ApiEndpoint::post('/createActionListGroup', 'createActionListGroup')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Action List Group')
            )
            ->endpoint(
                ApiEndpoint::post('/importActionListGroup', 'createActionListGroup4')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Import Action List Group')
            );
    }
}
