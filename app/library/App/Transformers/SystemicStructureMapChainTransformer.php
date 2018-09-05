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

    public function includeSystemicStructureMapItemsFrom($model)
    {
        return $this->item($model->getSystemicStructureMapItemsFrom(), new SystemicStructureMapItemsTransformer());
    }

    public function includeSystemicStructureMapItemsTo($model)
    {
        return $this->item($model->getSystemicStructureMapItemsTo(), new SystemicStructureMapItemsTransformer());
    }
}
