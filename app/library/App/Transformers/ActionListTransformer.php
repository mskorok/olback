<?php

namespace App\Transformers;

use App\Model\ActionList;
use PhalconRest\Transformers\Transformer;

class ActionListTransformer extends Transformer
{
    protected $modelClass = ActionList::class;

    protected $availableIncludes = [
        'ActionListGroup'
    ];

    public function includeActionListGroup($model)
    {
        return $this->item($model->getActionListGroup(), new ActionListGroupTransformer());
    }
}
