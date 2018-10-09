<?php

namespace App\Transformers;

use App\Model\SingleReport;
use PhalconRest\Transformers\ModelTransformer;

class SingleReportTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SingleReport::class;
        $this->availableIncludes = [
            'Process', 'User'
        ];
    }

    public function includeProcess(SingleReport $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }

    public function includeUser(SingleReport $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
