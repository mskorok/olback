<?php

namespace App\Transformers;

use App\Model\Answer;
use PhalconRest\Transformers\ModelTransformer;

class AnswerTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = Answer::class;
        $this->availableIncludes = [
            'SurveyQuestions', 'User'
        ];
    }

    public function includeSurveyQuestions(Answer $model)
    {
        return $this->item($model->getSurveyQuestions(), new SurveyQuestionsTransformer());
    }

    public function includeUser(Answer $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
