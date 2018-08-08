<?php

namespace App\Transformers;

use App\Model\SystemicStructureMapChain;
use PhalconRest\Transformers\Transformer;

class SystemicStructureMapChainTransformer extends Transformer
{
    protected $modelClass = SystemicStructureMapChain::class;

    protected $availableIncludes = [
        'SystemicStructureMapItemsFrom', 'SystemicStructureMapItemsTo'
    ];

    public function includeSystemicStructureMapItemsFrom($model)
    {
        return $this->item($model->getSystemicStructureMapItemsFrom(), new SystemicStructureMapItemsTransformer());
    }

    public function includeSystemicStructureMapItemsTo($model)
    {
        return $this->item($model->getSystemicMapStructureMapItemsTo(), new SystemicStructureMapItemsTransformer());
    }
}
