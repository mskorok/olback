<?php

namespace App\Transformers;

use App\Model\OptionAnswer;
use PhalconRest\Transformers\ModelTransformer;

class OptionAnswerTransformer extends ModelTransformer
{

    public function __construct()
    {
        $this->modelClass = OptionAnswer::class;
        $this->availableIncludes = [
            'QuestionGroup'
        ];
    }

    public function includeQuestionGroup(OptionAnswer $model)
    {
        return $this->item($model->getQuestionGroup(), new QuestionGroupsTransformer());
    }
}
