<?php

namespace App\Transformers;

use App\Model\Organization;
use PhalconRest\Transformers\ModelTransformer;

class OrganizationTransformer extends ModelTransformer
{
    protected $modelClass = Organization::class;

    protected $availableIncludes = [
        'Departments',
        'Groups',
        'Process',
        'Survey',
        'SurveyTemplates',
        'SystemicMap',
        'SystemicStructureMap',
        'UserOrganization',
        'User'
    ];

    public function includeDepartments($model)
    {
        return $this->collection($model->getDepartments(), new DepartmentTransformer);
    }

    public function includeGroups($model)
    {
        return $this->collection($model->getGroups(), new GroupTransformer);
    }

    public function includeProcess($model)
    {
        return $this->collection($model->getProcess(), new ProcessTransformer);
    }

    public function includeSurvey($model)
    {
        return $this->collection($model->getSurvey(), new SurveyTransformer);
    }

    public function includeSurveyTemplates($model)
    {
        return $this->collection($model->getSurveyTemplates(), new SurveyTemplatesTransformer);
    }

    public function includeSystemicMap($model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer);
    }

    public function includeSystemicStructureMap($model)
    {
        return $this->collection($model->getSystemicStructureMap(), new SystemicStructureMapTransformer);
    }

    public function includeUserOrganization($model)
    {
        return $this->collection($model->getUserOrganization(), new UserOrganizationTransformer);
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
