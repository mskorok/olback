<?php

namespace App\Transformers;

use App\Model\Group;
use PhalconRest\Transformers\ModelTransformer;

class GroupTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Group::class;
        $this->availableIncludes = [
            'SystemicMapItems', 'SystemicStructureMapItems', 'Organization', 'User'
        ];
    }

    public function includeSystemicMapItems(Group $model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer);
    }

    public function includeSystemicStructureMapItems(Group $model)
    {
        return $this->collection($model->getSystemicStructureMapItems(), new SystemicStructureMapItemsTransformer);
    }

    public function includeOrganization(Group $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser(Group $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
