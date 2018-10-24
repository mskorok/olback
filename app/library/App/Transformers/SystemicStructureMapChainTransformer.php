<?php

namespace App\Transformers;

use App\Model\SystemicStructureMapChain;
use PhalconRest\Transformers\ModelTransformer;

class SystemicStructureMapChainTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicStructureMapChain::class;
        $this->availableIncludes = [
            'SystemicStructureMapItemsFrom', 'SystemicStructureMapItemsTo'
        ];
    }

    public function includeSystemicStructureMapItemsFrom(SystemicStructureMapChain $model)
    {
        return $this->item($model->getSystemicStructureMapItemsFrom(), new SystemicStructureMapItemsTransformer());
    }

    public function includeSystemicStructureMapItemsTo(SystemicStructureMapChain $model)
    {
        return $this->item($model->getSystemicStructureMapItemsTo(), new SystemicStructureMapItemsTransformer());
    }
}
