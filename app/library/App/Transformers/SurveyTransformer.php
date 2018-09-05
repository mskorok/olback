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
            'SurveyQuestions',
            'SurveyTemplatesQuestions',
            'Organization',
            'User'
        ];
    }

    public function includeProcess0(Survey $model)
    {
        return $this->collection($model->getProcess0(), new ProcessTransformer());
    }

    public function includeProcess30(Survey $model)
    {
        return $this->collection($model->getProcess30(), new ProcessTransformer());
    }

    public function includeProcess31(Survey $model)
    {
        return $this->collection($model->getProcess31(), new ProcessTransformer());
    }

    public function includeSurveyQuestions(Survey $model)
    {
        return $this->collection($model->getSurveyQuestions(), new ActionListGroupTransformer());
    }

    public function includeOrganization(Survey $model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser(Survey $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeActionListGroup(Survey $model)
    {
        return $this->item($model->getActionListGroup(), new ActionListGroupTransformer());
    }
}
