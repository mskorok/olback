<?php

namespace App\Transformers;

use App\Model\Department;
use PhalconRest\Transformers\Transformer;

class DepartmentTransformer extends Transformer
{
    protected $modelClass = Department::class;

    protected $availableIncludes = [
        'SystemicMap', 'UserDepartment', 'Organization'
    ];

    public function includeSystemicMap($model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer);
    }

    public function includeUserDepartment($model)
    {
        return $this->collection($model->getUserDepartment(), new UserDepartmentTransformer);
    }

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }
}
