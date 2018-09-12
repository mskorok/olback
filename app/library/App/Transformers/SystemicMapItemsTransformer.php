<?php

namespace App\Transformers;

use App\Model\SystemicMapItems;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapItemsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SystemicMapItems::class;
        $this->availableIncludes = [
            'SystemicMapChainFrom', 'SystemicMapChainTo', 'Groups', 'SystemicMap', 'User', 'Survey'
        ];
    }

    public function includeSystemicMapChainFrom(SystemicMapItems $model)
    {
        return $this->collection($model->getSystemicMapChainFrom(), new SystemicMapChainTransformer());
    }

    public function includeSystemicMapChainTo(SystemicMapItems $model)
    {
        return $this->collection($model->getSystemicMapChainTo(), new SystemicMapChainTransformer());
    }

    public function includeGroups(SystemicMapItems $model)
    {
        return $this->item($model->getGroups(), new GroupTransformer());
    }

    public function includeSystemicMap(SystemicMapItems $model)
    {
        return $this->item($model->getSystemicMap(), new SystemicMapTransformer());
    }

    public function includeUser(SystemicMapItems $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeSurvey(SystemicMapItems $model)
    {
        return $this->item($model->getSurvey(), new SurveyTransformer());
    }
}
