<?php

namespace App\Transformers;

use App\Model\ActionList;
use App\Model\SystemicMapItems;
use PhalconRest\Transformers\Transformer;

class SystemicMapItemsTransformer extends Transformer
{
    protected $modelClass = SystemicMapItems::class;

    protected $availableIncludes = [
        'SystemicMapChainFrom', 'SystemicMapChainTo', 'Groups', 'SystemicMap', 'User'
    ];

    public function includeSystemicMapChainFrom($model)
    {
        return $this->collection($model->getSystemicMapChainFrom(), new SystemicMapChainTransformer());
    }

    public function includeSystemicMapChainTo($model)
    {
        return $this->collection($model->getSystemicMapChainTo(), new SystemicMapChainTransformer());
    }

    public function includeGroups($model)
    {
        return $this->item($model->getGroups(), new GroupTransformer());
    }

    public function includeSystemicMap($model)
    {
        return $this->item($model->getSystemicMap(), new SystemicMapTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
