<?php

namespace App\Transformers;

use App\Model\ProcessYearSurvey;
use PhalconRest\Transformers\ModelTransformer;

class ProcessYearSurveyTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ProcessYearSurvey::class;
        $this->availableIncludes = [
            'Survey', 'Process'
        ];
    }

    public function includeSurvey(ProcessYearSurvey $model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer);
    }

    public function includeProcess(ProcessYearSurvey $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer);
    }
}
