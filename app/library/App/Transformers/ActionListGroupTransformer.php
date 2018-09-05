<?php

namespace App\Transformers;

use App\Model\ActionListGroup;
use PhalconRest\Transformers\ModelTransformer;

class ActionListGroupTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ActionListGroup::class;
        $this->availableIncludes = [
            'ActionList', 'SystemicMap', 'User'
        ];
    }

    public function includeActivity(ActionListGroup $model)
    {
        return $this->collection($model->getActionList(), new ActionListTransformer());
    }

    public function includeSystemicMap(ActionListGroup $model)
    {
        return $this->item($model->getSystemicMap(), new SystemicMapTransformer());
    }

    public function includeUser(ActionListGroup $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
