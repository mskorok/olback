<?php

namespace App\Transformers;

use App\Model\SystemicStructureMapItems;
use PhalconRest\Transformers\Transformer;

class SystemicStructureMapItemsTransformer extends Transformer
{
    protected $modelClass = SystemicStructureMapItems::class;

    protected $availableIncludes = [
        'SystemicStructureMapChainFrom', 'SystemicStructureMapChainTo', 'Groups', 'SystemicStructureMap', 'User'
    ];

    public function includeSystemicStructureMapChainFrom($model)
    {
        return $this->item($model->getSystemicStructureMapChainFrom(), new SystemicStructureMapChainTransformer());
    }

    public function includeSystemicStructureMapChainTo($model)
    {
        return $this->item($model->getSystemicStructureMapChainTo(), new SystemicStructureMapChainTransformer());
    }

    public function includeGroups($model)
    {
        return $this->item($model->getGroups(), new GroupTransformer());
    }

    public function includeSystemicStructureMap($model)
    {
        return $this->item($model->getSystemicStructureMap(), new SystemicStructureMapTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
