<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Group;
use App\Transformers\GroupTransformer;
use App\Controllers\StatisticsController;
use App\Constants\AclRoles;

class StatisticsResource extends ApiResource {

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

            ->endpoint(ApiEndpoint::get('/dashboard', 'getDashboardStats')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
                ->description('get dashboard statistics')
            )
            ->endpoint(ApiEndpoint::get('/getReportsByProcess/{id}', 'getReportsByProcess')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED,AclRoles::AUTHORIZED)
                ->description('getReportsByProcess')
            );
    }

}
