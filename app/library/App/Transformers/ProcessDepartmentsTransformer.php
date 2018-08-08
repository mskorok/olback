<?php

namespace App\Transformers;

use App\Model\ProcessDepartments;
use PhalconRest\Transformers\Transformer;

class ProcessDepartmentsTransformer extends Transformer
{
    protected $modelClass = ProcessDepartments::class;

    protected $availableIncludes = [
        'Departments', 'Process'
    ];

    public function includeDepartments($model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
