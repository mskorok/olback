<?php

namespace App\Transformers;

use App\Model\ProcessDepartments;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Transformers\Transformer;

class ProcessDepartmentsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ProcessDepartments::class;
        $this->availableIncludes = [
            'Departments', 'Process'
        ];
    }

    public function includeDepartments(ProcessDepartments $model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeProcess(ProcessDepartments $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
