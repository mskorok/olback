<?php

namespace App\Transformers;

use App\Model\SystemicMap;
use PhalconRest\Transformers\ModelTransformer;

class SystemicMapTransformer extends ModelTransformer
{
    protected $modelClass = SystemicMap::class;

    protected function excludedProperties()
    {
        //return ['password'];
    }
}
