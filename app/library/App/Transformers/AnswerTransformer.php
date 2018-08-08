<?php

namespace App\Transformers;

use App\Model\ActionList;
use PhalconRest\Transformers\Transformer;

class AnswerTransformer extends Transformer
{
    protected $modelClass = ActionList::class;

    protected $availableIncludes = [
        'SurveyQuestions', 'User'
    ];

    public function includeSurveyQuestions($model)
    {
        return $this->item($model->getSurveyQuestions(), new SurveyQuestionsTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
