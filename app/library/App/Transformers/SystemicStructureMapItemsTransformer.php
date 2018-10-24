<?php

namespace App\Transformers;

use App\Model\SystemicStructureMapItems;
use PhalconRest\Transformers\ModelTransformer;

class SystemicStructureMapItemsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicStructureMapItems::class;
        $this->availableIncludes = [
            'SystemicStructureMapChainFrom',
            'SystemicStructureMapChainTo',
            'Groups',
            'SystemicStructureMap',
            'User',
            'Survey'
        ];
    }

    public function includeSystemicStructureMapChainFrom(SystemicStructureMapItems $model)
    {
        return $this->item($model->getSystemicStructureMapChainFrom(), new SystemicStructureMapChainTransformer());
    }

    public function includeSystemicStructureMapChainTo(SystemicStructureMapItems $model)
    {
        return $this->item($model->getSystemicStructureMapChainTo(), new SystemicStructureMapChainTransformer());
    }

    public function includeGroups(SystemicStructureMapItems $model)
    {
        return $this->item($model->getGroups(), new GroupTransformer());
    }

    public function includeSystemicStructureMap(SystemicStructureMapItems $model)
    {
        return $this->item($model->getSystemicStructureMap(), new SystemicStructureMapTransformer());
    }

    public function includeUser(SystemicStructureMapItems $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeSurvey(SystemicStructureMapItems $model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }
}
