<?php

namespace App\Resources;

use App\Model\ProcessYearSurvey;
use App\Transformers\ProcessYearSurveyTransformer;
use PhalconApi\Constants\PostedDataMethods;
use PhalconRest\Api\ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Constants\AclRoles;
use PhalconRest\Mvc\Controllers\CrudResourceController;

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
            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(CrudResourceController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create())
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update()->postedDataMethod(PostedDataMethods::POST))
            ->endpoint(ApiEndpoint::remove());
    }
}
