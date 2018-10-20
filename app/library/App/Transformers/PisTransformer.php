<?php

namespace App\Transformers;

use App\Model\Pis;
use PhalconRest\Transformers\ModelTransformer;

class PisTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Pis::class;
        $this->availableIncludes = [
            'Process', 'User'
        ];
    }

    public function includeProcess(Pis $model)
    {
        return $this->collection($model->getProcess(), new ProcessTransformer());
    }

    public function includeUser(Pis $model)
    {
        return $this->collection($model->getUser(), new UserTransformer());
    }
}
