<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Organization;
use App\Transformers\OrganizationTransformer;
use App\Controllers\OrganizationController;
use App\Constants\AclRoles;

class OrganizationResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Organization')
            ->model(Organization::class)
            ->expectsJsonData()
            ->transformer(OrganizationTransformer::class)
            ->handler(OrganizationController::class)
            ->itemKey('organization')
            ->collectionKey('organization')
//            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
            ->endpoint(
                ApiEndpoint::get('/organization', 'getOrgs')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get organizations')
            )
            ->endpoint(
                ApiEndpoint::put('/', 'updateOrg')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('update organizations')
            )
            ->endpoint(
                ApiEndpoint::post('/organization', 'createOrg')
                    ->allow(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('create an organization')
            );
    }
}
