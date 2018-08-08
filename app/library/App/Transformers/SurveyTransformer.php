<?php

namespace App\Transformers;

use App\Model\Survey;
use PhalconRest\Transformers\Transformer;

class SurveyTransformer extends Transformer
{
    protected $modelClass = Survey::class;

    protected $availableIncludes = [
        'Process0',
        'Process30',
        'Process31',
        'SurveyQuestions',
        'SurveyTemplatesQuestions',
        'Organization',
        'User'
    ];

    public function includeProcess0($model)
    {
        return $this->collection($model->getProcess0(), new ProcessTransformer());
    }

    public function includeProcess30($model)
    {
        return $this->collection($model->getProcess30(), new ProcessTransformer());
    }

    public function includeProcess31($model)
    {
        return $this->collection($model->getProcess31(), new ProcessTransformer());
    }

    public function includeSurveyQuestions($model)
    {
        return $this->collection($model->getSurveyQuestions(), new ActionListGroupTransformer());
    }

    public function includeOrganization($model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeActionListGroup($model)
    {
        return $this->item($model->getActionListGroup(), new ActionListGroupTransformer());
    }
}
