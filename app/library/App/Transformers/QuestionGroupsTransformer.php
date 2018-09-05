<?php

namespace App\Transformers;

use App\Model\QuestionGroups;
use PhalconRest\Transformers\ModelTransformer;

class QuestionGroupsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = QuestionGroups::class;
        $this->availableIncludes = [
            'SurveyQuestions', 'SurveyTemplatesQuestions'
        ];
    }

    public function includeSurveyQuestions(QuestionGroups $model)
    {
        return $this->collection($model->getSurveyQuestions(), new SurveyQuestionsTransformer());
    }

    public function includeSurveyTemplatesQuestions(QuestionGroups $model)
    {
        return $this->collection($model->getSurveyTemplatesQuestions(), new SurveyTemplatesQuestionsTransformer());
    }
}
