<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Group;
use App\Transformers\GroupTransformer;
use App\Controllers\StatisticsController;
use App\Constants\AclRoles;

class StatisticsResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('Statistics')
//            ->model(Statistics::class)
            ->expectsJsonData()
            // ->transformer(OrganizationTransformer::class)ss
            ->handler(StatisticsController::class)
            ->itemKey('Statistics')
            ->collectionKey('Statistics')
            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
            ->endpoint(
                ApiEndpoint::get('/dashboard', 'getDashboardStats')
//                    ->allow(AclRoles::UNAUTHORIZED)
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get dashboard statistics')
            )
            ->endpoint(
                ApiEndpoint::get('/dashboard/indices', 'getDashboardIndices')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('get dashboard data for processes')
            )
            ->endpoint(
                ApiEndpoint::get('/getReportsBySurvey/{id}', 'getReportsBySurvey')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('getReportsBySurvey')
            )
            ->endpoint(
                ApiEndpoint::get('/getReportsBySurveyAndUser/{id}/{userId}', 'getReportsBySurveyAndUser')
                    ->allow(AclRoles::MANAGER)
                    ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                    ->description('getReportsBySurveyAndUser')
            );
    }
}
