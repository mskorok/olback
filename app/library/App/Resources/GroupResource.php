<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Group;
use App\Transformers\GroupTransformer;
use App\Controllers\GroupController;
use App\Constants\AclRoles;

class GroupResource extends ApiResource {

    public function initialize()
    {
      $this

          ->name('Group')
          ->model(Group::class)
          ->expectsJsonData()
          // ->transformer(OrganizationTransformer::class)ss
          ->handler(GroupController::class)
          ->itemKey('group')
          ->collectionKey('group')
          ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)

          ->endpoint(ApiEndpoint::get('/', 'getGroups')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('get all groups')
          )

          ->endpoint(ApiEndpoint::post('/', 'createGroup')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('create a group')
          )
          ->endpoint(ApiEndpoint::put('/{id}', 'updateGroup')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('create a group')
          )
          ->endpoint(ApiEndpoint::delete('/{id}', 'deleteGroup')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
              ->description('create a group')
          );
    }

}
