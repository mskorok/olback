<?php

namespace App\Transformers;

use App\Model\ActionList;
use App\Model\QuestionGroups;
use PhalconRest\Transformers\Transformer;

class QuestionGroupsTransformer extends Transformer
{
    protected $modelClass = QuestionGroups::class;

    protected $availableIncludes = [
        'SurveyQuestions', 'SurveyTemplatesQuestions'
    ];

    public function includeSurveyQuestions($model)
    {
        return $this->collection($model->getSurveyQuestions(), new SurveyQuestionsTransformer());
    }

    public function includeSurveyTemplatesQuestions($model)
    {
        return $this->collection($model->getSurveyTemplatesQuestions(), new SurveyTemplatesQuestionsTransformer());
    }
}
