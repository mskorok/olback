<?php

namespace App\Transformers;

use App\Model\GroupTemplate;
use PhalconRest\Transformers\ModelTransformer;

class GroupTemplateTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = GroupTemplate::class;
    }
}
