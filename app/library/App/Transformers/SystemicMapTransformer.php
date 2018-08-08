<?php

namespace App\Transformers;

use App\Model\SystemicMap;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapTransformer extends ModelTransformer
{
    protected $modelClass = SystemicMap::class;

    protected $availableIncludes = [
        'ActionListGroup', 'SystemicMapItems', 'Departments', 'Organization', 'Process'
    ];

    public function includeActionListGroup($model)
    {
        return $this->collection($model->getActionListGroup(), new ActionListGroupTransformer());
    }

    public function includeSystemicMapItems($model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer());
    }

    public function includeDepartments($model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
