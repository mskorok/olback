<?php

namespace App\Transformers;

use App\Model\SurveyQuestion;
use PhalconRest\Transformers\Transformer;

class SurveyQuestionsTransformer extends Transformer
{
    protected $modelClass = SurveyQuestion::class;

    protected $availableIncludes = [
        'Answers', 'QuestionGroups', 'Survey'
    ];

    public function includeQuestionGroups($model)
    {
        return $this->item($model->getQuestionGroups(), new QuestionGroupsTransformer());
    }

    public function includeSurvey($model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }

    public function includeAnswers($album)
    {
        return $this->collection($album->getAnswers(), new AnswerTransformer());
    }
}
