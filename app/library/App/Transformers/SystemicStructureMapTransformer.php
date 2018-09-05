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
            'Organization', 'Process'
        ];
    }

    public function includeOrganization(SystemicStructureMap $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcess(SystemicStructureMap $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
