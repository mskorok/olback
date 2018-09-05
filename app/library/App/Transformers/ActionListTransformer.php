<?php

namespace App\Transformers;

use App\Model\ActionList;
use PhalconRest\Transformers\ModelTransformer;

class ActionListTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ActionList::class;
        $this->availableIncludes = [
            'ActionListGroup'
        ];
    }

    public function includeActionListGroup(ActionList $model)
    {
        return $this->item($model->getActionListGroup(), new ActionListGroupTransformer());
    }
}
