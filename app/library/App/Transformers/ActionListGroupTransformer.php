<?php

namespace App\Transformers;

use App\Model\ActionListGroup;
use PhalconRest\Transformers\ModelTransformer;

class ActionListGroupTransformer extends ModelTransformer
{
    protected $modelClass = ActionListGroup::class;

    protected $availableIncludes = [
        'ActionList', 'SystemicMap', 'User'
    ];

    public function includeActivity($model)
    {
        return $this->collection($model->getActionList(), new ActionListTransformer());
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
