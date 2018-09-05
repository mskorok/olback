<?php

namespace App\Transformers;

use App\Model\Department;
use PhalconRest\Transformers\ModelTransformer;

class DepartmentTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Department::class;
        $this->availableIncludes = [
            'SystemicMap', 'UserDepartment', 'Organization'
        ];
    }

    public function includeSystemicMap(Department$model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer);
    }

    public function includeUserDepartment(Department$model)
    {
        return $this->collection($model->getUserDepartment(), new UserDepartmentTransformer);
    }

    public function includeOrganization(Department$model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }
}
