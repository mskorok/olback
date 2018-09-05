<?php

namespace App\Transformers;

use App\Model\ActionList;
use App\Model\ProcessUsers;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Transformers\Transformer;

class ProcessUsersTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ProcessUsers::class;
        $this->availableIncludes = [
            'Process', 'User'
        ];
    }

    public function includeProcess(ProcessUsers $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }

    public function includeUser(ProcessUsers $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
