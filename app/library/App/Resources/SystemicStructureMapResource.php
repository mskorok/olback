<?php

namespace App\Resources;

use App\Controllers\SystemicStructureMapController;
use App\Model\SystemicStructureMap;
use App\Transformers\SystemicStructureMapTransformer;
use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Constants\AclRoles;

class SystemicStructureMapResource extends ApiResource
{
    public function initialize()
    {
        $this
            ->name('SystemicMap')
            ->model(SystemicStructureMap::class)
            ->expectsJsonData()
             ->transformer(SystemicStructureMapTransformer::class)
            ->handler(SystemicStructureMapController::class)
            ->itemKey('systemic-structure-map')
            ->collectionKey('systemic-structure-maps')
            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
            //Map
            ->endpoint(
                ApiEndpoint::get('/getAll', 'getSystemicStructureMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get systemic structure maps')
            )
            ->endpoint(
                ApiEndpoint::get('/getSystemicStructureMap/{id}', 'getSystemicStructureMapByProcess')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Structure Map By Process')
            )

            ->endpoint(
                ApiEndpoint::post('/createSystemicStructureMap', 'createSystemicStructureMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create systemic structure maps')
            )
            ->endpoint(
                ApiEndpoint::put('/updateSystemicStructureMap/{id}', 'updateSystemicStructureMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Update Systemic Structure Map')
            )
            ->endpoint(
                ApiEndpoint::delete('/{id}', 'deleteSystemicStructureMap')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Systemic Structure Map')
            )
            // Items
            ->endpoint(
                ApiEndpoint::get('/getItem/{id}', 'getSystemicStructureItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Structure Item')
            )
            ->endpoint(
                ApiEndpoint::post('/createSystemicStructureMapItem', 'createSystemicStructureMapItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Systemic Structure Map Item')
            )
            ->endpoint(
                ApiEndpoint::put('/updateSystemicStructureItem/{id}', 'updateSystemicStructureItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('update updateSystemicStructureItem')
            )
            ->endpoint(
                ApiEndpoint::delete('/deleteSystemicStructureItem/{id}', 'deleteSystemicStructureItem')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Systemic Structure Item')
            )
            //Chain
            ->endpoint(
                ApiEndpoint::get('/getSystemicStructureChain/{id}', 'getSystemicStructureChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('getStructureChain')
            )
            ->endpoint(
                ApiEndpoint::post('/createSystemicStructureChain', 'createSystemicStructureChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Structure Chain')
            )
            ->endpoint(
                ApiEndpoint::delete('/deleteSystemicStructureChain/{id}', 'deleteSystemicStructureChain')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Delete Structure Chain')
            )
            //Tree
            ->endpoint(
                ApiEndpoint::get('/getItemTree/{id}', 'getSystemicStructureItemTree')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Get Systemic Structure Item Tree')
            )
            //Action List
            ->endpoint(
                ApiEndpoint::post('/createActionListGroup', 'createActionListGroup')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Create Action List Group')
            )
            ->endpoint(
                ApiEndpoint::post('/importActionListGroup', 'importActionListGroup')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('Import Action List Group')
            );
    }
}
