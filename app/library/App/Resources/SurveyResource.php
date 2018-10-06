<?php

namespace App\Resources;

use App\Transformers\SurveyTransformer;
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
             ->transformer(SurveyTransformer::class)
            ->handler(SurveyController::class)
            ->itemKey('survey')
            ->collectionKey('survey')
            ->deny(AclRoles::UNAUTHORIZED, AclRoles::USER)
            ->endpoint(
                ApiEndpoint::post('/', 'createSurveyDefinition')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('create survey')
            )
            ->endpoint(
                ApiEndpoint::get('/', 'getSurveyDefinition')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get survey')
            )
            ->endpoint(
                ApiEndpoint::get('/initProcess/{id}', 'initProcess')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('init process')
            )
            ->endpoint(
                ApiEndpoint::get('/getSurveyAnswers/{id}', 'getSurveyAnswers')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get Survey Answers')
            )
            ->endpoint(
                ApiEndpoint::get('/changeProcessStatus/{id}', 'changeProcessStatus')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('change Process Status')
            )
            ->endpoint(
                ApiEndpoint::get('/getUserSurveyAnswers', 'getUserSurveyAnswers')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('get User Survey Answers')
            )
            ->endpoint(
                ApiEndpoint::get('/getAvailableSurveys', 'availableUserSurveys')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('Get Available Surveys Data')
            )
            ->endpoint(
                ApiEndpoint::put('/{id}', 'updateSurveyDefinition')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('update survey')
            )
            ->endpoint(
                ApiEndpoint::post('/answers/{id}', 'createAnswer')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('create answer')
            )
            //getQuestionGroups
            ->endpoint(
                ApiEndpoint::get('/getQuestionGroups', 'getQuestionGroups')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('getQuestionGroups')
            )
            ->endpoint(
                ApiEndpoint::post('/addQuestion/{id}', 'createQuestion')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('add survey question')
            )
            ->endpoint(
                ApiEndpoint::post('/addWpHelp', 'helpPage')
                ->allow(AclRoles::MANAGER)
                ->deny(AclRoles::UNAUTHORIZED, AclRoles::AUTHORIZED)
                ->description('help Page')
            )
            ->endpoint(
                ApiEndpoint::get('/getQuestions/{id}', 'getQuestion')
                ->allow(AclRoles::USER, AclRoles::MANAGER, AclRoles::AUTHORIZED)
                ->deny(AclRoles::UNAUTHORIZED)
                ->description('get survey question')
            )->endpoint(
                ApiEndpoint::get('/action/aar/create/{id}', 'createActionAAR')
                    ->allow(AclRoles::USER, AclRoles::MANAGER, AclRoles::AUTHORIZED)
                    ->deny(AclRoles::UNAUTHORIZED)
                    ->description('Create Action AAR')
            );
    }
}
