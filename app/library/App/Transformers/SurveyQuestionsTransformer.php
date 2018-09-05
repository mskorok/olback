<?php

namespace App\Transformers;

use App\Model\SurveyQuestion;
use PhalconRest\Transformers\ModelTransformer;

class SurveyQuestionsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SurveyQuestion::class;
        $this->availableIncludes = [
            'Answers', 'QuestionGroups', 'Survey'
        ];
    }

    public function includeQuestionGroups(SurveyQuestion $model)
    {
        return $this->item($model->getQuestionGroups(), new QuestionGroupsTransformer());
    }

    public function includeSurvey(SurveyQuestion $model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }

    public function includeAnswers(SurveyQuestion $album)
    {
        return $this->collection($album->getAnswers(), new AnswerTransformer());
    }
}
