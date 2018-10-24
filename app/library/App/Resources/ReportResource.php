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
            ->deny(AclRoles::UNAUTHORIZED)
            ->endpoint(
                ApiEndpoint::get('/render-single', 'renderSingleReport')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Single Report data')
            )
            ->endpoint(
                ApiEndpoint::get('/render-group', 'renderGroupReport')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Group Report data')
            )->endpoint(
                ApiEndpoint::get('/single', 'singleReport')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Single Report link')
            )->endpoint(
                ApiEndpoint::get('/group', 'groupReport')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Group Report link')
            )->endpoint(
                ApiEndpoint::get('/create-group', 'groupReportCreate')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
                    ->description('get Group Report create')
            )
            ->endpoint(
                ApiEndpoint::get('/create-single', 'singleReportCreate')
                    ->allow(AclRoles::USER)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('get Group Report create')
            );
    }
}
