<?php

namespace App\Transformers;

use App\Model\SurveyTemplateQuestion;
use PhalconRest\Transformers\Transformer;

class SurveyTemplatesQuestionsTransformer extends Transformer
{
    protected $modelClass = SurveyTemplateQuestion::class;

    protected $availableIncludes = [
        'QuestionGroup', 'Survey'
    ];

    public function includeQuestionGroup($model)
    {
        return $this->item($model->getQuestionGroup(), new QuestionGroupsTransformer());
    }

    public function includeSurvey($model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }
}
