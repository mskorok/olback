<?php

namespace App\Transformers;

use App\Model\User;
use PhalconRest\Transformers\ModelTransformer;

class OrganizationTransformer extends ModelTransformer
{
    protected $modelClass = Organization::class;

    protected function excludedProperties()
    {
        //return ['password'];
    }
}
