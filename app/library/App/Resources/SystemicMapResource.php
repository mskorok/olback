<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\SystemicMap;
use App\Controllers\SystemicMapController;
use App\Constants\AclRoles;
//a
class SystemicMapResource extends ApiResource
{
    public function initialize()
    {
        $this

          ->name('SystemicMap')
          ->model(SystemicMap::class)
          ->expectsJsonData()
          // ->transformer(OrganizationTransformer::class)
          ->handler(SystemicMapController::class)
          ->itemKey('systemicmap')
          ->collectionKey('systemicmap')
          ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)

          ->endpoint(ApiEndpoint::get('/getAll', 'getSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get systemicmaps')
          )
            ->endpoint(ApiEndpoint::get('/getSystemic/{id}', 'getSystemicMapByProcess')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get systemicmaps')
            )
            ->endpoint(ApiEndpoint::get('/getSystemicStructureMap/{id}', 'getSystemicStructureMapByProcess')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get getSystemicStructureMapByProcess')
            )
          ->endpoint(ApiEndpoint::get('/getItem/{id}', 'getSystemicItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get systemicmaps')
          )
          ->endpoint(ApiEndpoint::get('/getItemTree/{id}', 'getSystemicItemTree')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get systemicmaps')
          )

          ->endpoint(ApiEndpoint::post('/create', 'createSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create systemicmaps')

          )->endpoint(ApiEndpoint::post('/createSystemicStructureMap', 'createSystemicStructureMap')
                  ->allow(AclRoles::MANAGER)
                  ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                  ->description('create structure systemicmaps')

          )->endpoint(ApiEndpoint::post('/createItem', 'createSystemicMapItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create systemicmapsItem')
          )
            //createSystemicStructureMapItem
            ->endpoint(ApiEndpoint::post('/createSystemicStructureMapItem', 'createSystemicStructureMapItem')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('create SystemicStructureMap item')
            )
            ->endpoint(ApiEndpoint::put('/updateSystemicStructureMap/{id}', 'updateSystemicStructureMap')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('update SystemicStructureMap')
            )
          ->endpoint(ApiEndpoint::put('/{id}', 'updateSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('update systemicmapsItem')
          )
          ->endpoint(ApiEndpoint::put('/item/{id}', 'updateSystemicItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('update systemicmapsItem')
          )

            //updateSystemicStructureItem
            ->endpoint(ApiEndpoint::put('/updateSystemicStructureItem/{id}', 'updateSystemicStructureItem')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('update updateSystemicStructureItem')
            )
          ->endpoint(ApiEndpoint::delete('/{id}', 'deleteSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('delete systemicmapsItem')
          )
          ->endpoint(ApiEndpoint::delete('/item/{id}', 'deleteSystemicItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('delete systemicmapsItem')
          )
            //deleteSystemicStructureItem
            ->endpoint(ApiEndpoint::delete('/deleteSystemicStructureItem/{id}', 'deleteSystemicStructureItem')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('delete deleteSystemicStructureItem')
            )

            ->endpoint(ApiEndpoint::get('/getSystemicStructureItem/{id}/{type}', 'getSystemicStructureItem')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('getSystemicStructureItem')
            )
          ->endpoint(ApiEndpoint::post('/createActionListGroup', 'createActionListGroup')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create Action List Group'))
          ->endpoint(ApiEndpoint::post('/importActionListGroup', 'createActionListGroup4')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create Action List Group'));
    }
}
