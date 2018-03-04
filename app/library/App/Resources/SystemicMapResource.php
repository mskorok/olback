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
            ->endpoint(ApiEndpoint::post('/answers', 'createAnswer')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('create answer')
            )
          ->endpoint(ApiEndpoint::post('/create', 'createSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create systemicmaps')
          )->endpoint(ApiEndpoint::post('/createItem', 'createSystemicMapItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create systemicmapsItem')
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
