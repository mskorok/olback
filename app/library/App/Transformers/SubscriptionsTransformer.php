<?php

namespace App\Transformers;

use App\Model\Subscriptions;
use PhalconRest\Transformers\ModelTransformer;

class SubscriptionsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Subscriptions::class;
        $this->availableIncludes = [
            'Organization', 'Subscribers', 'User', 'Users'
        ];
    }

    public function includeOrganization(Subscriptions $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeSubscribers(Subscriptions $model)
    {
        return $this->collection($model->getSubscribers(), new SubscribersTransformer());
    }

    public function includeUser(Subscriptions $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeUsers(Subscriptions $model)
    {
        return $this->collection($model->getUsers(), new UserTransformer());
    }
}
