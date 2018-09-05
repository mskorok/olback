<?php

namespace App\Transformers;

use App\Model\Organization;
use PhalconRest\Transformers\ModelTransformer;

class OrganizationTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Organization::class;
        $this->availableIncludes = [
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
    }


    public function includeDepartments(Organization $model)
    {
        return $this->collection($model->getDepartments(), new DepartmentTransformer);
    }

    public function includeGroups(Organization $model)
    {
        return $this->collection($model->getGroups(), new GroupTransformer);
    }

    public function includeProcess(Organization $model)
    {
        return $this->collection($model->getProcess(), new ProcessTransformer);
    }

    public function includeSurvey(Organization $model)
    {
        return $this->collection($model->getSurvey(), new SurveyTransformer);
    }

    public function includeSurveyTemplates(Organization $model)
    {
        return $this->collection($model->getSurveyTemplates(), new SurveyTemplatesTransformer);
    }

    public function includeSystemicMap(Organization $model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer);
    }

    public function includeSystemicStructureMap(Organization $model)
    {
        return $this->collection($model->getSystemicStructureMap(), new SystemicStructureMapTransformer);
    }

    public function includeUserOrganization(Organization $model)
    {
        return $this->collection($model->getUserOrganization(), new UserOrganizationTransformer);
    }

    public function includeUser(Organization $model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
