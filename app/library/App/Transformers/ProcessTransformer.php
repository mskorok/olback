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
            'Creator',
            'ProcessDepartments',
            'ProcessOrganizations',
            'ProcessUsers',
            'ProcessYearSurvey',
            'SystemicMap',
            'SystemicStructureMap',
            'Organization',
            'Subscriptions',
            'SurveyInitial',
            'SurveyEvaluation',
            'SurveyAAR',
            'Reality',
            'Vision'
        ];
    }

    public function includeCreator(Process $model)
    {
        return $this->item($model->getCreator(), new UserTransformer());
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

    public function includeSubscriptions(Process $model)
    {
        return $this->item($model->getSubscriptions(), new SubscriptionsTransformer());
    }

    public function includeSurveyInitial(Process $model)
    {
        return $this->item($model->getSurveyInitial(), new SurveyTransformer());
    }

    public function includeSurveyEvaluation(Process $model)
    {
        return $this->item($model->getSurveyEvaluation(), new SurveyTransformer());
    }

    public function includeSurveyAAR(Process $model)
    {
        return $this->item($model->getSurveyAAR(), new SurveyTransformer());
    }

    public function includeReality(Process $model)
    {
        return $this->item($model->getReality(), new SurveyTransformer);
    }

    public function includeVision(Process $model)
    {
        return $this->item($model->getVision(), new SurveyTransformer);
    }
}
