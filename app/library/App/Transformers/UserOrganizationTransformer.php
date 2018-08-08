<?php

namespace App\Transformers;

use App\Model\UserOrganization;
use PhalconRest\Transformers\Transformer;

class UserOrganizationTransformer extends Transformer
{
    protected $modelClass = UserOrganization::class;

    protected $availableIncludes = [
        'Organization', 'User'
    ];

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
