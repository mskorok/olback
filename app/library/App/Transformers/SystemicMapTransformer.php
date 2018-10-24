<?php

namespace App\Transformers;

use App\Model\SystemicMap;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicMap::class;
        $this->availableIncludes = [
            'ActionListGroup', 'Creator', 'SystemicMapItems', 'Departments', 'Organization', 'Process'
        ];
    }

    public function includeActionListGroup(SystemicMap $model)
    {
        return $this->collection($model->getActionListGroup(), new ActionListGroupTransformer());
    }

    public function includeCreator(SystemicMap $model)
    {
        return $this->item($model->getCreator(), new UserTransformer());
    }

    public function includeSystemicMapItems(SystemicMap $model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer());
    }

    public function includeDepartments(SystemicMap $model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeOrganization(SystemicMap $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcess(SystemicMap $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
