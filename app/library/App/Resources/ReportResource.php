<?php

namespace App\Resources;

use App\Controllers\ReportController;
use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Constants\AclRoles;

class ReportResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Report')
            ->expectsJsonData()
            ->handler(ReportController::class)
            ->itemKey('report')
            ->collectionKey('reports')
//            ->deny(AclRoles::UNAUTHORIZED)
            ->allow(AclRoles::UNAUTHORIZED)
            ->endpoint(
                ApiEndpoint::get('/render-single/{id}', 'renderSingleGroup')
                    ->allow(AclRoles::UNAUTHORIZED)
//                    ->allow(AclRoles::MANAGER)
//                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get Single Report data')
            )
            ->endpoint(
                ApiEndpoint::get('/render-group/{id}', 'renderGroupReport')
                    ->allow(AclRoles::UNAUTHORIZED)
//                    ->allow(AclRoles::MANAGER)
//                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get Group Report data')
            )->endpoint(
                ApiEndpoint::get('/single/{id}', 'singleReport')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Single Report link')
            )->endpoint(
                ApiEndpoint::get('/group/{id}', 'groupReport')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get Group Report link')
            );
    }
}
