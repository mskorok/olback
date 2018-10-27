<?php

namespace App\Transformers;

use App\Model\User;
use PhalconRest\Transformers\ModelTransformer;

class UserTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = User::class;
        $this->availableIncludes = [
            'ActionListGroup',
            'Answers',
            'Groups',
            'Organization',
            'Pis',
            'Processes',
            'ProcessUsers',
            'SessionSubscription',
            'SingleReport',
            'Survey',
            'SurveyTemplates',
            'Subscribers',
            'Subscription',
            'Subscriptions',
            'SystemicMapItems',
            'SystemicStructureMapItems',
            'UserDepartment',
            'UserOrganization'
        ];
    }

    protected function excludedProperties(): array
    {
        return ['password'];
    }

    public function includeActionListGroup(User $model)
    {
        return $this->collection($model->getActionListGroup(), new ActionListGroupTransformer());
    }

    public function includeAnswers(User $model)
    {
        return $this->collection($model->getAnswers(), new AnswerTransformer());
    }

    public function includeGroups(User $model)
    {
        return $this->collection($model->getGroups(), new GroupTransformer());
    }

    public function includeOrganization(User $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includePis(User $model)
    {
        return $this->collection($model->getPis(), new PisTransformer());
    }

    public function includeProcesses(User $model)
    {
        return $this->item($model->getProcesses(), new ProcessTransformer());
    }

    public function includeProcessUsers(User $model)
    {
        return $this->collection($model->getProcessUsers(), new ProcessUsersTransformer());
    }

    public function includeSessionSubscription(User $model)
    {
        return $this->item($model->getSessionSubscription(), new SessionSubscriptionTransformer());
    }

    public function includeSingleReport(User $model)
    {
        return $this->collection($model->getSingleReport(), new SingleReportTransformer());
    }

    public function includeSurvey(User $model)
    {
        return $this->collection($model->getSurvey(), new SurveyTransformer());
    }

    public function includeSurveyTemplates(User $model)
    {
        return $this->collection($model->getSurveyTemplates(), new SurveyTemplatesTransformer());
    }
    public function includeSubscribers(User $model)
    {
        return $this->collection($model->getSubscribers(), new SubscribersTransformer());
    }
    public function includeSubscription(User $model)
    {
        return $this->item($model->getSubscription(), new SubscriptionsTransformer());
    }
    public function includeSubscriptions(User $model)
    {
        return $this->collection($model->getSubscriptions(), new SubscriptionsTransformer());
    }

    public function includeSystemicMapItems(User $model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer());
    }

    public function includeSystemicStructureMapItems(User $model)
    {
        return $this->collection($model->getSystemicStructureMapItems(), new SystemicStructureMapItemsTransformer());
    }

    public function includeUserDepartment(User $model)
    {
        return $this->collection($model->getUserDepartment(), new UserDepartmentTransformer());
    }

    public function includeUserOrganization(User $model)
    {
        return $this->collection($model->getUserOrganization(), new UserOrganizationTransformer());
    }
}
