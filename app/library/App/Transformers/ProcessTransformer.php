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
    protected $modelClass = Process::class;

    protected $availableIncludes = [
        'ProcessDepartments',
        'ProcessOrganizations',
        'ProcessUsers',
        'SystemicMap',
        'SystemicStructureMap',
        'Organization',
        'Survey0',
        'Survey30',
        'Survey31'
    ];

    public function includeProcessDepartments($model)
    {
        return $this->collection($model->getProcessDepartments(), new ProcessDepartmentsTransformer());
    }

    public function includeProcessOrganizations($model)
    {
        return $this->collection($model->getProcessOrganizations(), new ProcessOrganizationsTransformer());
    }

    public function includeProcessUsers($model)
    {
        return $this->collection($model->getProcessUsers(), new ProcessUsersTransformer());
    }

    public function includeSystemicMap($model)
    {
        return $this->collection($model->getSystemicMap(), new SystemicMapTransformer());
    }

    public function includeSystemicStructureMap($model)
    {
        return $this->collection($model->getSystemicStructureMap(), new SystemicStructureMapTransformer());
    }

    public function includeOrganization($model)
    {
        return $this->collection($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeSurvey0($model)
    {
        return $this->item($model->getSurvey0(), new SurveyTransformer());
    }

    public function includeSurvey30($model)
    {
        return $this->item($model->getSurvey3_0(), new SurveyTransformer());
    }

    public function includeSurvey31($model)
    {
        return $this->item($model->getSurvey3_1(), new SurveyTransformer());
    }
}
