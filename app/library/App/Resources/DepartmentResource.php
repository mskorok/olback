<?php

namespace App\Resources;

use App\Transformers\DepartmentTransformer;
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
           ->transformer(DepartmentTransformer::class)
          ->handler(DepartmentController::class)
          ->itemKey('department')
          ->collectionKey('department')
          ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
          ->endpoint(ApiEndpoint::post('/', 'createDepartment')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create department'))
            ->endpoint(ApiEndpoint::post('/assignUserDepartment/{userId}', 'assignUserDepartment')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('assign User2Department'))
          ->endpoint(ApiEndpoint::get('/', 'getDepartment')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get department'))
            ->endpoint(ApiEndpoint::get('/getUserDepartments/{id}', 'getUserDepartments')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get user department'))
            ->endpoint(ApiEndpoint::put('/updateUserDepartments/{id}', 'updateUserDepartments')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('update user department'))
            ->endpoint(ApiEndpoint::delete('/{id}', 'deleteDepartment')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('deleteDepartment'))
          ->endpoint(ApiEndpoint::put('/{id}', 'updateDepartment')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('update department'));
    }
}
