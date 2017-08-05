<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\SystemicMap;
use App\Model\SystemicMapItem;
use App\Transformers\SystemicMapTransformer;
use App\Controllers\SystemicMapController;
use App\Constants\AclRoles;

class SystemicMapResource extends ApiResource {

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
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('get systemicmaps')
          )
          ->endpoint(ApiEndpoint::get('/getItem/{id}', 'getSystemicItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('get systemicmaps')
          )
          ->endpoint(ApiEndpoint::post('/create', 'createSystemicMap')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('create systemicmaps')
          )->endpoint(ApiEndpoint::post('/createItem', 'createSystemicMapItem')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('create systemicmapsItem')
          );
    }

}