<?php

namespace App\Transformers;

use App\Model\ActionList;
use App\Model\ProcessUsers;
use PhalconRest\Transformers\Transformer;

class ProcessUsersTransformer extends Transformer
{
    protected $modelClass = ProcessUsers::class;

    protected $availableIncludes = [
        'Process', 'User'
    ];

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
