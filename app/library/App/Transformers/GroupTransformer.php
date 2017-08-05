<?php

namespace App\Transformers;

use App\Model\Group;
use PhalconRest\Transformers\ModelTransformer;

class GroupTransformer extends ModelTransformer
{
    protected $modelClass = Group::class;

    protected function excludedProperties()
    {
        //return ['password'];
    }
}
