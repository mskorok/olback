<?php

namespace App\Transformers;

use App\Model\SystemicMapChain;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapChainTransformer extends ModelTransformer
{
    protected $modelClass = SystemicMapChain::class;

    protected $availableIncludes = [
        'SystemicMapItemsFrom', 'SystemicMapItemsTo'
    ];



    public function includeSystemicMapItemsFrom($model)
    {
        return $this->item($model->getSystemicMapItemsFrom(), new SystemicMapItemsTransformer());
    }

    public function includeSystemicMapItemsTo($model)
    {
        return $this->item($model->SystemicMapItemsTo(), new SystemicMapItemsTransformer());
    }
}
