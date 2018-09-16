<?php

namespace App\Transformers;

use App\Model\Survey;
use PhalconRest\Transformers\ModelTransformer;

class SurveyTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Survey::class;
        $this->availableIncludes = [
            'Process0',
            'Process30',
            'Process31',
            'ProcessReality',
            'ProcessVision',
            'ProcessYearRealitySurvey',
            'ProcessYearSurvey',
            'ProcessYearVisionSurvey',
            'SurveyQuestions',
            'SystemicMapItems',
            'Organization',
            'User'
        ];
    }

    public function includeProcess0(Survey $model)
    {
        return $this->item($model->getProcess0(), new ProcessTransformer());
    }

    public function includeProcess30(Survey $model)
    {
        return $this->item($model->getProcess30(), new ProcessTransformer());
    }

    public function includeProcess31(Survey $model)
    {
        return $this->item($model->getProcess31(), new ProcessTransformer());
    }

    public function includeProcessReality(Survey $model)
    {
        return $this->item($model->getProcessReality(), new ProcessTransformer());
    }

    public function includeProcessVision(Survey $model)
    {
        return $this->item($model->getProcessVision(), new ProcessTransformer());
    }

    public function includeProcessYearSurvey(Survey $model)
    {
        return $this->collection($model->getProcessYearSurvey(), new ProcessYearSurveyTransformer());
    }

    public function includeProcessYearRealitySurvey(Survey $model)
    {
        return $this->collection($model->getProcessYearRealitySurvey(), new ProcessYearSurveyTransformer());
    }

    public function includeProcessYearVisionSurvey(Survey $model)
    {
        return $this->collection($model->getProcessYearVisionSurvey(), new ProcessYearSurveyTransformer());
    }

    public function includeSurveyQuestions(Survey $model)
    {
        return $this->collection($model->getSurveyQuestions(), new SurveyQuestionsTransformer());
    }

    public function includeSystemicMapItems(Survey $model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer());
    }

    public function includeOrganization(Survey $model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser(Survey $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
