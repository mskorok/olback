<?php

namespace App\Transformers;

use App\Model\SurveyTemplate;
use PhalconRest\Transformers\ModelTransformer;

class SurveyTemplatesTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SurveyTemplate::class;
        $this->availableIncludes = [
            'Organization', 'User', 'SurveyTemplatesQuestions',
        ];
    }

    public function includeOrganization(SurveyTemplate $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser(SurveyTemplate $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeSurveyTemplatesQuestions(SurveyTemplate $model)
    {
        return $this->collection($model->getSurveyTemplatesQuestions(), new SurveyTemplatesQuestionsTransformer());
    }
}
