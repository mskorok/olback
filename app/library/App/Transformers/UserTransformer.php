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
            'ProcessUsers',
            'Survey',
            'SurveyTemplates',
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
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcessUsers(User $model)
    {
        return $this->collection($model->getProcessUsers(), new ProcessUsersTransformer());
    }

    public function includeSurvey(User $model)
    {
        return $this->collection($model->getSurvey(), new SurveyTransformer());
    }

    public function includeSurveyTemplates(User $model)
    {
        return $this->collection($model->getSurveyTemplates(), new SurveyTemplatesTransformer());
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
