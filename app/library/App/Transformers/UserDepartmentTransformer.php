<?php

namespace App\Transformers;

use App\Model\UserDepartment;
use PhalconRest\Transformers\ModelTransformer;

class UserDepartmentTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = UserDepartment::class;
        $this->availableIncludes = [
            'Departments', 'User'
        ];
    }

    public function includeDepartments(UserDepartment $model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeUser(UserDepartment $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
