<?php

namespace App\Transformers;

use App\Model\UserOrganization;
use PhalconRest\Transformers\ModelTransformer;

class UserOrganizationTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = UserOrganization::class;
        $this->availableIncludes = [
            'Organization', 'User'
        ];
    }

    public function includeOrganization(UserOrganization $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser(UserOrganization $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
