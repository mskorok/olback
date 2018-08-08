<?php

namespace App\Transformers;

use App\Model\Group;
use PhalconRest\Transformers\ModelTransformer;

class GroupTransformer extends ModelTransformer
{
    protected $modelClass = Group::class;

    protected $availableIncludes = [
        'SystemicMapItems', 'SystemicStructureMapItems', 'Organization', 'User'
    ];

    public function includeSystemicMapItems($model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer);
    }

    public function includeSystemicStructureMapItems($model)
    {
        return $this->collection($model->getSystemicStructureMapItems(), new SystemicStructureMapItemsTransformer);
    }

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
