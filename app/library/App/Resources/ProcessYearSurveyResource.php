<?php

namespace App\Resources;

use App\Controllers\ProcessYearSurveyController;
use App\Model\ProcessYearSurvey;
use App\Transformers\ProcessYearSurveyTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;

class ProcessYearSurveyResource extends ApiResource
{

    public function initialize()
    {
        $this
            ->name('ProcessYearSurvey')
            ->model(ProcessYearSurvey::class)
            ->expectsJsonData()
            ->transformer(ProcessYearSurveyTransformer::class)
            ->itemKey('yearSurvey')
            ->collectionKey('yearSurveys')
            ->handler(ProcessYearSurveyController::class)
            ->endpoint(ApiEndpoint::all()->deny(AclRoles::UNAUTHORIZED))
            ->endpoint(ApiEndpoint::create()->deny(AclRoles::UNAUTHORIZED))
            ->endpoint(ApiEndpoint::find()->deny(AclRoles::UNAUTHORIZED))
            ->endpoint(ApiEndpoint::update()->deny(AclRoles::UNAUTHORIZED)->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove())->deny(AclRoles::UNAUTHORIZED)
            ->endpoint(ApiEndpoint::get('/full/{id}', 'getFullSurveyData')
                ->allow(AclRoles::UNAUTHORIZED)
                ->description('Count for products from best destinations'));
    }
}
