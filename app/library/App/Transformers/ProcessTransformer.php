<?php
/**
 * Created by PhpStorm.
 * User: thomaschatzidimitris
 * Date: 13/02/2018
 * Time: 23:59
 */

namespace App\Transformers;

use App\Model\Process;
use PhalconRest\Transformers\ModelTransformer;

class ProcessTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Process::class;
        $this->availableIncludes = [
            'ProcessDepartments',
            'ProcessOrganizations',
            'ProcessUsers',
            'ProcessYearSurvey',
            'SystemicMap',
            'SystemicStructureMap',
            'Organization',
            'Survey0',
            'Survey30',
            'Survey31'
        ];
    }

    public function includeProcessDepartments(Process $model)
    {
        return $this->collection($model->getProcessDepartments(), new ProcessDepartmentsTransformer());
    }

    public function includeProcessOrganizations(Process $model)
    {
        return $this->collection($model->getProcessOrganizations(), new ProcessOrganizationsTransformer());
    }

    public function includeProcessUsers(Process $model)
    {
        return $this->collection($model->getProcessUsers(), new ProcessUsersTransformer());
    }

    public function includeProcessYearSurvey(Process $model)
    {
        return $this->collection($model->getProcessYearSurvey(), new ProcessYearSurveyTransformer());
    }

    public function includeSystemicMap(Process $model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer());
    }

    public function includeSystemicStructureMap(Process $model)
    {
        return $this->collection($model->getSystemicStructureMap(), new SystemicStructureMapTransformer());
    }

    public function includeOrganization(Process $model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeSurvey0(Process $model)
    {
        return $this->item($model->getSurvey0(), new SurveyTransformer());
    }

    public function includeSurvey30(Process $model)
    {
        return $this->item($model->getSurvey30(), new SurveyTransformer());
    }

    public function includeSurvey31(Process $model)
    {
        return $this->item($model->getSurvey31(), new SurveyTransformer());
    }
}
