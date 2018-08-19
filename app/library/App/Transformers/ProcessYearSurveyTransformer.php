<?php

namespace App\Transformers;

use App\Model\ProcessYearSurvey;
use PhalconRest\Transformers\Transformer;

class ProcessYearSurveyTransformer extends Transformer
{
    protected $modelClass = ProcessYearSurvey::class;

    protected $availableIncludes = [
        'Survey', 'Process'
    ];

    public function includeSurvey($model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer);
    }

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer);
    }
}
