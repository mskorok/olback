<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Department;
use App\Controllers\DepartmentController;
use App\Constants\AclRoles;

class DepartmentResource extends ApiResource
{
    public function initialize()
    {
        $this

          ->name('Department')
          ->model(Department::class)
          ->expectsJsonData()
          // ->transformer(OrganizationTransformer::class)
          ->handler(DepartmentController::class)
          ->itemKey('department')
          ->collectionKey('department')
          ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
          ->endpoint(ApiEndpoint::post('/', 'createDepartment')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create department'))
          ->endpoint(ApiEndpoint::get('/', 'getDepartment')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get department'));
    }
}
