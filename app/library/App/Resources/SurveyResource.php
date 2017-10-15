<?php

namespace App\Resources;

use PhalconRest\Api\ApiResource;
use PhalconRest\Api\ApiEndpoint;
use App\Model\Survey;
use App\Controllers\SurveyController;
use App\Constants\AclRoles;

class SurveyResource extends ApiResource
{
    public function initialize()
    {
        $this

          ->name('Survey')
          ->model(Survey::class)
          ->expectsJsonData()
          // ->transformer(OrganizationTransformer::class)
          ->handler(SurveyController::class)
          ->itemKey('survey')
          ->collectionKey('survey')
          ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
          ->endpoint(ApiEndpoint::post('/', 'createSurveyDefinition')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('create survey'))
          ->endpoint(ApiEndpoint::get('/', 'getSurveyDefinition')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('get survey'))
          ->endpoint(ApiEndpoint::put('/{id}', 'updateSurveyDefinition')
              ->allow(AclRoles::MANAGER)
              ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
              ->description('update survey'));
    }
}
