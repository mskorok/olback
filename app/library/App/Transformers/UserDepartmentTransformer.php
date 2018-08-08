<?php

namespace App\Transformers;

use App\Model\ActionList;
use App\Model\UserDepartment;
use PhalconRest\Transformers\Transformer;

class UserDepartmentTransformer extends Transformer
{
    protected $modelClass = UserDepartment::class;

    protected $availableIncludes = [
        'Departments', 'User'
    ];

    public function includeDepartments($model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
