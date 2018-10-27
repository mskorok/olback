<?php

namespace App\Transformers;

use App\Model\SessionSubscription;
use PhalconRest\Transformers\ModelTransformer;

class SessionSubscriptionTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = SessionSubscription::class;
        $this->availableIncludes = [
            'User', 'Subscriptions'
        ];
    }

    public function includeUser(SessionSubscription $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }

    public function includeSubscriptions(SessionSubscription $model)
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }
}
