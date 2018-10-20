<?php

namespace App\Transformers;

use App\Model\Subscribers;
use PhalconRest\Transformers\ModelTransformer;

class SubscribersTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Subscribers::class;
        $this->availableIncludes = [
            'Subscriptions', 'User'
        ];
    }

    public function includeSubscriptions(Subscribers $model)
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }

    public function includeUser(Subscribers $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
