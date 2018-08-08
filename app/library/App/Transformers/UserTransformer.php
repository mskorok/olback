<?php

namespace App\Transformers;

use App\Model\User;
use PhalconRest\Transformers\ModelTransformer;

class UserTransformer extends ModelTransformer
{
    protected $modelClass = User::class;

    protected function excludedProperties()
    {
        return ['password'];
    }

    protected $availableIncludes = [
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

    public function includeActionListGroup($model)
    {
        return $this->collection($model->getActionListGroup(), new ActionListGroupTransformer());
    }

    public function includeAnswers($model)
    {
        return $this->collection($model->getAnswers(), new AnswerTransformer());
    }

    public function includeGroups($model)
    {
        return $this->collection($model->getGroups(), new GroupTransformer());
    }

    public function includeOrganization($model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeProcessUsers($model)
    {
        return $this->collection($model->getProcessUsers(), new ProcessUsersTransformer());
    }

    public function includeSurvey($model)
    {
        return $this->collection($model->getSurvey(), new SurveyTransformer());
    }

    public function includeSurveyTemplates($model)
    {
        return $this->collection($model->getSurveyTemplates(), new SurveyTemplatesTransformer());
    }

    public function includeSystemicMapItems($model)
    {
        return $this->collection($model->getSystemicMapItems(), new SystemicMapItemsTransformer());
    }

    public function includeSystemicStructureMapItems($model)
    {
        return $this->collection($model->getSystemicStructureMapItems(), new SystemicStructureMapItemsTransformer());
    }

    public function includeUserDepartment($model)
    {
        return $this->collection($model->getUserDepartment(), new UserDepartmentTransformer());
    }

    public function includeUserOrganization($model)
    {
        return $this->collection($model->getUserOrganization(), new UserOrganizationTransformer());
    }
}
