<?php

namespace App\Transformers;

use App\Model\SystemicStructureMap;
use PhalconRest\Transformers\Transformer;

class SystemicStructureMapTransformer extends Transformer
{
    protected $modelClass = SystemicStructureMap::class;

    protected $availableIncludes = [
        'Organization', 'Process'
    ];

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }
}
