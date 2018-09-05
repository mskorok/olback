<?php

namespace App\Transformers;

use App\Model\SurveyTemplateQuestion;
use PhalconRest\Transformers\ModelTransformer;

class SurveyTemplatesQuestionsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SurveyTemplateQuestion::class;
        $this->availableIncludes = [
            'QuestionGroup', 'Survey'
        ];
    }

    public function includeQuestionGroup(SurveyTemplateQuestion $model)
    {
        return $this->item($model->getQuestionGroup(), new QuestionGroupsTransformer());
    }

    public function includeSurvey(SurveyTemplateQuestion $model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }
}
