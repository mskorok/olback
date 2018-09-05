<?php

namespace App\Transformers;

use App\Model\SystemicMapChain;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapChainTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicMapChain::class;
        $this->availableIncludes = [
            'SystemicMapItemsFrom', 'SystemicMapItemsTo'
        ];
    }



    public function includeSystemicMapItemsFrom(SystemicMapChain $model)
    {
        return $this->item($model->getSystemicMapItemsFrom(), new SystemicMapItemsTransformer());
    }

    public function includeSystemicMapItemsTo(SystemicMapChain $model)
    {
        return $this->item($model->getSystemicMapItemsTo(), new SystemicMapItemsTransformer());
    }
}
