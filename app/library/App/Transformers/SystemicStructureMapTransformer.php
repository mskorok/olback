<?php

namespace App\Transformers;

use App\Model\SystemicStructureMap;
use PhalconRest\Transformers\ModelTransformer;

class SystemicStructureMapTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicStructureMap::class;
        $this->availableIncludes = [
            'Creator', 'Departments', 'Organization', 'Process', 'Subscriptions'
        ];
    }


    public function includeCreator(SystemicStructureMap $model)
    {
        return $this->item($model->getCreator(), new UserTransformer());
    }

    public function includeDepartments(SystemicStructureMap $model)
    {
        return $this->item($model->getDepartments(), new DepartmentTransformer());
    }

    public function includeOrganization(SystemicStructureMap $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcess(SystemicStructureMap $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }

    public function includeSubscriptions(SystemicStructureMap $model)
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }
}
